<?php

namespace App\Services\Auction;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JsonProviderReadOnlyService
{
    public function getBarang(string $idLelang): array
    {
        try {
            $rows = DB::table('ms_procurement_barang')
                ->select(['id', 'nama_barang', 'nilai_hps', 'id_kurs'])
                ->where('id_procurement', $idLelang)
                ->orderBy('id', 'asc')
                ->get();

            $rates = $this->loadKursRateMap($idLelang);
            $usedRateFallback = false;
            $payload = [];

            foreach ($rows as $row) {
                $hpsInIdr = $this->convertToIdr($row->nilai_hps, $row->id_kurs, $rates, $usedRateFallback);

                $payload[] = [
                    'id' => $row->id,
                    'name' => (string) $row->nama_barang,
                    'hps' => $row->nilai_hps,
                    'hps_in_idr' => $hpsInIdr,
                ];
            }

            $headers = [
                'X-Pilot-Data-Source' => 'db-readonly',
                'X-Pilot-Data-Status' => $usedRateFallback ? 'partial-rate-fallback' : 'ok',
            ];

            return [
                'payload' => $payload,
                'headers' => $headers,
            ];
        } catch (QueryException $e) {
            return $this->degradedEmpty('get_barang', $idLelang, $e);
        }
    }

    public function getPeserta(string $idLelang): array
    {
        try {
            $rows = DB::table('ms_procurement_peserta as a')
                ->leftJoin('ms_vendor as b', 'a.id_vendor', '=', 'b.id')
                ->selectRaw('a.id_vendor as id, b.name as name')
                ->where('a.id_proc', $idLelang)
                ->orderBy('a.id', 'asc')
                ->get();

            $payload = [];
            foreach ($rows as $row) {
                $payload[] = [
                    'id' => $row->id,
                    'name' => $row->name !== null ? (string) $row->name : '',
                ];
            }

            return [
                'payload' => $payload,
                'headers' => [
                    'X-Pilot-Data-Source' => 'db-readonly',
                    'X-Pilot-Data-Status' => 'ok',
                ],
            ];
        } catch (QueryException $e) {
            return $this->degradedEmpty('get_peserta', $idLelang, $e);
        }
    }

    public function getInitialData(string $idLelang, string $idBarang): array
    {
        try {
            $barang = $this->loadBarangMeta($idBarang);
            $series = $this->buildInitialSeries($idLelang, $idBarang, false);
            $last = $this->buildChartUpdateData($idLelang);

            return [
                'payload' => [
                    'id' => $idBarang,
                    'name' => $barang['name'],
                    'subtitle' => $barang['subtitle'],
                    'data' => $series,
                    'last' => $last,
                    'time' => $this->timestampNow(),
                ],
                'headers' => [
                    'X-Pilot-Data-Source' => 'db-readonly',
                    'X-Pilot-Data-Status' => 'ok',
                ],
            ];
        } catch (QueryException $e) {
            return $this->degradedWithPayload(
                'get_initial_data',
                [
                    'id' => $idBarang,
                    'name' => '',
                    'subtitle' => '',
                    'data' => [],
                    'last' => [],
                    'time' => $this->timestampNow(),
                ],
                $e,
                [
                    'id_lelang' => $idLelang,
                    'id_barang' => $idBarang,
                ]
            );
        }
    }

    public function getChartUpdate(string $idLelang): array
    {
        try {
            return [
                'payload' => [
                    'data' => $this->buildChartUpdateData($idLelang),
                    'time' => $this->timestampNow(),
                ],
                'headers' => [
                    'X-Pilot-Data-Source' => 'db-readonly',
                    'X-Pilot-Data-Status' => 'ok',
                ],
            ];
        } catch (QueryException $e) {
            return $this->degradedWithPayload(
                'get_chart_update',
                [
                    'data' => [],
                    'time' => $this->timestampNow(),
                ],
                $e,
                [
                    'id_lelang' => $idLelang,
                ]
            );
        }
    }

    private function loadKursRateMap(string $idLelang): array
    {
        try {
            $rows = DB::table('ms_procurement_kurs')
                ->select(['id_kurs', 'rate'])
                ->where('id_procurement', $idLelang)
                ->get();
        } catch (QueryException $e) {
            Log::warning('pilot-auction get_barang rate lookup degraded', [
                'id_lelang' => $idLelang,
                'sql_state' => $e->errorInfo[0] ?? null,
                'error_code' => $e->errorInfo[1] ?? null,
                'message' => $e->getMessage(),
            ]);

            return [];
        }

        $map = [];
        foreach ($rows as $row) {
            $map[(string) $row->id_kurs] = $row->rate;
        }

        return $map;
    }

    private function loadBarangMeta(string $idBarang): array
    {
        $row = DB::table('ms_procurement_barang as a')
            ->leftJoin('tb_kurs as b', 'a.id_kurs', '=', 'b.id')
            ->select(['a.nama_barang', 'a.nilai_hps', 'b.symbol'])
            ->where('a.id', $idBarang)
            ->first();

        if ($row === null) {
            return [
                'name' => '',
                'subtitle' => '',
            ];
        }

        $name = $row->nama_barang !== null ? (string) $row->nama_barang : '';
        $subtitle = '';

        if ($row->nilai_hps !== null && is_numeric($row->nilai_hps)) {
            $symbol = $row->symbol !== null ? trim((string) $row->symbol) : '';
            $subtitle = trim($symbol . ' ' . number_format((float) $row->nilai_hps));
        }

        return [
            'name' => $name,
            'subtitle' => $subtitle,
        ];
    }

    private function buildChartUpdateData(string $idLelang): array
    {
        $barangRows = DB::table('ms_procurement_barang')
            ->select(['id'])
            ->where('id_procurement', $idLelang)
            ->orderBy('id', 'asc')
            ->get();

        $payload = [];
        foreach ($barangRows as $barang) {
            $payload[] = [
                'id' => $barang->id,
                'data' => $this->buildInitialSeries($idLelang, (string) $barang->id, true),
            ];
        }

        return $payload;
    }

    private function buildInitialSeries(string $idLelang, string $idBarang, bool $latestOnly): array
    {
        $pesertaRows = DB::table('ms_procurement_peserta as a')
            ->leftJoin('ms_vendor as b', 'a.id_vendor', '=', 'b.id')
            ->selectRaw('a.id_vendor as id_vendor, b.name as name')
            ->where('a.id_proc', $idLelang)
            ->orderBy('a.id', 'asc')
            ->get();

        $payload = [];
        foreach ($pesertaRows as $peserta) {
            $offerQuery = DB::table('ms_penawaran')
                ->selectRaw('in_rate as nilai, entry_stamp')
                ->where('id_barang', $idBarang)
                ->where('id_vendor', $peserta->id_vendor);

            if ($latestOnly) {
                $offerQuery->orderBy('entry_stamp', 'desc')->limit(1);
            } else {
                $offerQuery->orderBy('entry_stamp', 'asc');
            }

            $offerRows = $offerQuery->get();
            $series = [];

            foreach ($offerRows as $offer) {
                $series[] = [
                    'x' => $offer->entry_stamp,
                    'y' => $offer->nilai,
                ];
            }

            $payload[] = [
                'name' => $peserta->name !== null ? (string) $peserta->name : '',
                'data' => $series,
            ];
        }

        return $payload;
    }

    private function convertToIdr($nilaiHps, $idKurs, array $rateMap, bool &$usedFallback)
    {
        if ((string) $idKurs === '1' || $idKurs === 1) {
            return $nilaiHps;
        }

        $rateKey = (string) $idKurs;
        if (!array_key_exists($rateKey, $rateMap) || !is_numeric($rateMap[$rateKey])) {
            $usedFallback = true;
            return $nilaiHps;
        }

        if (!is_numeric($nilaiHps)) {
            $usedFallback = true;
            return $nilaiHps;
        }

        return (float) $nilaiHps * (float) $rateMap[$rateKey];
    }

    private function timestampNow(): string
    {
        return date('Y-m-d H:i:s');
    }

    private function degradedEmpty(string $endpoint, string $idLelang, QueryException $e): array
    {
        return $this->degradedWithPayload($endpoint, [], $e, [
            'id_lelang' => $idLelang,
        ]);
    }

    private function degradedWithPayload(string $endpoint, array $payload, QueryException $e, array $context = []): array
    {
        $sqlState = $e->errorInfo[0] ?? 'unknown';
        $errorCode = $e->errorInfo[1] ?? 'unknown';

        Log::warning('pilot-auction readonly endpoint degraded to empty response', [
            'endpoint' => $endpoint,
            'id_lelang' => $context['id_lelang'] ?? null,
            'id_barang' => $context['id_barang'] ?? null,
            'sql_state' => $sqlState,
            'error_code' => $errorCode,
            'message' => $e->getMessage(),
        ]);

        return [
            'payload' => $payload,
            'headers' => [
                'X-Pilot-Data-Source' => 'degraded-empty',
                'X-Pilot-Data-Status' => 'db-unavailable-or-schema-mismatch',
                'X-Pilot-Error-SqlState' => (string) $sqlState,
                'X-Pilot-Error-Code' => (string) $errorCode,
            ],
        ];
    }
}
