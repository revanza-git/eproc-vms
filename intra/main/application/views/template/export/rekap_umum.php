<!DOCTYPE html>
<html lang="en">
<head>
    <title>Table Layout</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="<?= base_url().'assets/js/jquery-ja-master/jquery.ja.calendar.css' ?>" />
    <style>
        @import url("https://fonts.googleapis.com/css?family=Open+Sans:300,400,700");
        body {
            width: 100%;
            padding: 15px;
            font-family: "Open Sans";
            /*display: -webkit-flex;
            display: -moz-flex;
            display: -ms-flex;
            display: -o-flex;
            display: flex;
            -webkit-flex-direction: column;
            -moz-flex-direction: column;
            -ms-flex-direction: column;
            -o-flex-direction: column;
            flex-direction: column;
            -ms-align-items: center;
            align-items: center;*/
            background-color: #f9f9f9;
        }
        tr td {
            border: 1px solid #a0a0a0;
        }
        tr th {
            border: 1px solid #a0a0a0;
        }
        .no-border tr td {border: none;}
        .no-border tr th {border: none;}
        .export {
          background-color: #fff;
          width: 900px;
          margin: 5px 15px; }
          .export td, .export th {
            vertical-align: middle;
            text-align: center;
            border-spacing: none;
            padding: 5px; }
          .export th {
            padding: 5px; }
          .export-logo {
            margin: 15px;
            float: left; }
            .export-logo img {
              height: 55px; }
          .export-name {
            font-size: 1.2rem;
            font-weight: 400;
            margin: 15px;
            text-transform: uppercase; }
          .export-info li {
            display: flex; }
            .export-info li span {
              padding: 5px 15px;
              text-align: left; }
              .export-info li span:nth-child(1) {
                width: calc(40% - 15px * 2); }
              .export-info li span:nth-child(2) {
                width: calc(10% - 15px * 2); }
              .export-info li span:nth-child(3) {
                width: calc(60% - 15px * 2); }
          .export .sign-area {
            height: 100px;
            padding: 5px; }

        ul li {list-style: none;}
        .sign-name {
            text-decoration: underline;
        }
        .sign-position {
            font-size: 13px;
        }
        .smaller-text {
            font-size: 13px;
        }
        .is-yellow {
            background-color: #ffda5a;
        }
        .is-red {
            background-color: #e80e3a;
        }
        .is-blue {
            background-color: #0e459e;
        }
    </style>

    <link rel="stylesheet" type="text/css" href="jquery.ja.calendar.css" />
</head>

<body>

    <table class="export">
        <tr>
            <th>
                <div class="export-logo">
                    <img src="<?php echo base_url('assets/images/NUSANTARA-REGAS-2.png')?>">
                </div>
            </th>
        </tr>
    </table>
    <table class="export">
        <tr>
            <td>
                <div class="text-wrapper">
                    <div class="text-header">
                        KOMITMEN PENGADAAN BARANG/JASA <br> TAHUN 2018
                    </div>
                    <div class="text-content" style="text-align: left;">
                        Kami yang bertandatangan di  bawah ini, menyatakan komitmen atas pengadaan barang/jasa tahun 2018, sebagai berikut: 
                        <ul>
                            <li>
                                1. Pengadaan Barang/Jasa dilaksanakan berdasarkan pada prinsip, etika dan kebijakan Pengadaan Barang/Jasa Perusahaan;
                            </li>
                            <li>
                                2. Perencana pengadaan barang/jasa (daftar terlampir) telah disusun berdasarkan rencana kerja masing-masing fungsi di Perusahaan; 
                            </li>
                            <li>
                                3. Perubahan atas pengadaan barang/jasa dalam daftar perencanaan tersebut diatas, baik nama, ruang lingkup, jenis, metode dan jadwal pengadaan, wajib disampaikan tertulis kepada GM SOM dan Umum, dengan melampirkan Formulir Perubahan Perencanaan Pengadaan Barang/Jasa Tahun 2018 ("Formulir Perubahan") yang ditandatangani oleh Manager (Fungsi Leher) atau General Manager terkait; 
                            </li>
                            <li>
                                4 . Permintaan pengadaan barang/jasa diluar daftar perencanaan tersebut diatas atau pengadaan barang/jasa yang terlambatlmundur dari jadwal dalam daftar perencanaan terse but diatas, akan tetap diproses namun tidak menjadi prioritas dan mempertimbangkan beban kerja dari Fungsi Pengadaan pada saat permintaan pengadaan barang/jasa tersebut diajukan, kecuali ditentukan lain oleh Direktur Utama.
                            </li>
                        </ul>
                        Demikian komitmen ini kami buat dengan sebenar-benarnya, untuk digunakan sebagaimana mestinya
                    </div>
                </div>
            </td>
        </tr>
    </table>
    <table class="export no-border">
        <caption>Jakarta, 01 April 2018 </caption>
        <tr>
            <td colspan="6">
                <div class="sign-wrapper">
                    <div class="sign-area"></div>
                    <div class="sign-name">
                        Bara Frontasia
                    </div>
                    <div class="sign-position">
                        Direktur Operasi dan Komersial
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="sign-wrapper">
                    <div class="sign-area"></div>
                    <div class="sign-name">
                        Bara Frontasia
                    </div>
                    <div class="sign-position">
                        Direktur Operasi dan Komersial
                    </div>
                </div>
            </td>
            <td colspan="2">
                <div class="sign-wrapper">
                    <div class="sign-area"></div>
                    <div class="sign-name">
                        Bara Frontasia
                    </div>
                    <div class="sign-position">
                        Direktur Operasi dan Komersial
                    </div>
                </div>
            </td>
            <td colspan="2">
                <div class="sign-wrapper">
                    <div class="sign-area"></div>
                    <div class="sign-name">
                        Bara Frontasia
                    </div>
                    <div class="sign-position">
                        Direktur Operasi dan Komersial
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="sign-wrapper">
                    <div class="sign-area"></div>
                    <div class="sign-name">
                        Bara Frontasia
                    </div>
                    <div class="sign-position">
                        Direktur Operas; dan Komersial
                    </div>
                </div>
            </td>
            <td>
                <div class="sign-wrapper">
                    <div class="sign-area"></div>
                    <div class="sign-name">
                        Bara Frontasia
                    </div>
                    <div class="sign-position">
                        Direktur Operas; dan Komersial
                    </div>
                </div>
            </td>
            <td>
                <div class="sign-wrapper">
                    <div class="sign-area"></div>
                    <div class="sign-name">
                        Bara Frontasia
                    </div>
                    <div class="sign-position">
                        Direktur Operas; dan Komersial
                    </div>
                </div>
            </td>
            <td>
                <div class="sign-wrapper">
                    <div class="sign-area"></div>
                    <div class="sign-name">
                        Bara Frontasia
                    </div>
                    <div class="sign-position">
                        Direktur Operas; dan Komersial
                    </div>
                </div>
            </td>
            <td>
                <div class="sign-wrapper">
                    <div class="sign-area"></div>
                    <div class="sign-name">
                        Bara Frontasia
                    </div>
                    <div class="sign-position">
                        Direktur Operas; dan Komersial
                    </div>
                </div>
            </td>
            <td>
                <div class="sign-wrapper">
                    <div class="sign-area"></div>
                    <div class="sign-name">
                        Bara Frontasia
                    </div>
                    <div class="sign-position">
                        Direktur Operas; dan Komersial
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- <div>
        <table class="export">
            <tr>
                <td>
                    <div id="myCalendar"></div>
                </td>
            </tr>
        </table>
    </div> -->

    <div style="margin-top: 2rem">
        <table class="export smaller-text" style="width: calc(95% - 15px * 2)">
            <caption>
                Rencana Pengadaan Barang/Jasa Tahun 2018 <br> Metode Pelelangan, Pemilihan langsung dan Penunjukan langsung serta Swakelola
            </caption>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Pengguna <br> Barang/Jasa</th>
                <th rowspan="2">Paket Pengadaan Barang/Jasa</th>
                <th rowspan="2">Anggaran <br> (include PPN 10%) </th>
                <th rowspan="2">Metode <br> Pengadaan</th>
                <th rowspan="2">Keterangan</th>
                <th colspan="12">Persiapan & Permohonan Pengadaan (PP1) - Proses Pengadaan (PP2) - Pelaksanaan Pekerjaan (PP3)</th>
            </tr>
            <tr>
                <th>Jan - 18</th>
                <th>Jan - 18</th>
                <th>Jan - 18</th>
                <th>Jan - 18</th>
                <th>Jan - 18</th>
                <th>Jan - 18</th>
                <th>Jan - 18</th>
                <th>Jan - 18</th>
                <th>Jan - 18</th>
                <th>Jan - 18</th>
                <th>Jan - 18</th>
                <th>Jan - 18</th>
            </tr>
            <tr>
                <td></td>
                <td colspan="17" style="text-align: left;">Direktorat Operasi dan Komersial</td>
            </tr>
            <tr>
                <td rowspan="3">5</td>
                <td rowspan="3">Departement Quality Management & Quality Assurance</td>
                <td>Pengadaan Jasa Audit</td>
                <td>Rp. 300.000.000,00</td>
                <td>Pemilihan Langsung</td>
                <td>Lancar</td>
                <td colspan="3" class="is-yellow"></td>
                <td colspan="3" class="is-red"></td>
                <td colspan="3" class="is-blue"></td>
                <td colspan="3" class=""></td>
            </tr>
            <tr>
                <td>Pengadaan Jasa Audit</td>
                <td>Rp. 300.000.000,00</td>
                <td>Pemilihan Langsung</td>
                <td>Lancar</td>
                <td colspan="3" class="is-yellow"></td>
                <td colspan="3" class="is-red"></td>
                <td colspan="3" class="is-blue"></td>
                <td colspan="3" class=""></td>
            </tr>
            <tr>
                <td>Pengadaan Jasa Audit</td>
                <td>Rp. 300.000.000,00</td>
                <td>Pemilihan Langsung</td>
                <td>Lancar</td>
                <td colspan="3" class="is-yellow"></td>
                <td colspan="3" class="is-red"></td>
                <td colspan="3" class="is-blue"></td>
                <td colspan="3" class=""></td>
            </tr>
        </table>
    </div>

    <script type="text/javascript" src="<?= base_url().'assets/js/jquery-3.6.3.min.js' ?>"></script>

    <script type="text/javascript" src="<?= base_url().'assets/js/jquery-ja-master/jquery.ja.calendar.js' ?>"></script>

    <script>
        $(document).ready(function() {
            $("#myCalendar").jaCalendar();
        })
    </script>

</body>

</html>