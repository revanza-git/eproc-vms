<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migrate_year_anggaran extends CI_Controller
{
	public function index()
	{
		if (!is_cli()) {
			show_404();
		}

		$db = $this->load->database('default', true);

		$db->query("CREATE TABLE IF NOT EXISTS ms_fppbj_year_anggaran (
			id_fppbj INT NOT NULL,
			year_anggaran INT NOT NULL,
			PRIMARY KEY (id_fppbj, year_anggaran),
			KEY idx_year_anggaran (year_anggaran)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

		if (!$db->table_exists('ms_fppbj')) {
			echo "ms_fppbj not found in current database connection" . PHP_EOL;
			exit(0);
		}

		$rows = $db->select('id, year_anggaran')->where('del', 0)->get('ms_fppbj')->result_array();

		$inserted = 0;
		$skipped = 0;

		foreach ($rows as $row) {
			$id = (int) $row['id'];
			$raw = isset($row['year_anggaran']) ? (string) $row['year_anggaran'] : '';
			$raw = trim($raw);
			if ($raw === '') {
				$skipped++;
				continue;
			}

			$years = preg_split('/\\s*,\\s*/', $raw);
			if (!is_array($years) || count($years) === 0) {
				$skipped++;
				continue;
			}

			foreach ($years as $y) {
				$y = trim((string) $y);
				if ($y === '' || !preg_match('/^\\d{4}$/', $y)) {
					continue;
				}
				$year = (int) $y;
				$db->query(
					"INSERT IGNORE INTO ms_fppbj_year_anggaran (id_fppbj, year_anggaran) VALUES (?, ?)",
					array($id, $year)
				);
				$inserted += $db->affected_rows();
			}
		}

		echo "inserted={$inserted} skipped={$skipped}" . PHP_EOL;
	}
}
