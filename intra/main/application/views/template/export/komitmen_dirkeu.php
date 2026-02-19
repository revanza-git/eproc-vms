<!DOCTYPE html>
<html lang="en">
<head>
    <title>Table Layout</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        @import url("https://fonts.googleapis.com/css?family=Open+Sans:300,400,700");
        #dirkeu {
          border-collapse: collapse;
          width: 100%;
        }

        #dirkeu td, #dirkeu th {
          border: 1px solid #ddd;
          padding: 8px;
        }

        #dirkeu tr:nth-child(even){background-color: #f2f2f2;}

        #dirkeu .bold td {
            border: 2px solid #ddd;
        }
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
            border-collapse: collapse;
        }
        tr th {
            border: 1px solid #a0a0a0;
            border-collapse: collapse;
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
        <table id="dirkeu" class="export smaller-text" style="width: calc(95% - 15px * 2)" style="border-collapse: collapse;">
            <caption style="text-align: left; font-weight: 700">
                Rekapitulasi Rencana Pengadaan Barang/Jasa Tahun 2018
            </caption>
            <tr>
                <th>No.</th>
                <th>Satuan Kerja</th>
                <th>pelelangan</th>
                <th>Pemilihan Langsung</th>
                <th>Penunjukan Langsung</th>
                <th>Pengadaan Langsung</th>
                <th>Swakelola</th>
                <th>Keterangan</th>
            </tr>
            <tr class="bold">
                <td></td>
                <td style="text-align: right; font-weight: 700"><b>Direktorat Utama</b></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>1.</td>
                <td>Sekretaris Perusahaan</td>
                <td>-</td>
                <td>3</td>
                <td>1</td>
                <td>3</td>
                <td>15</td>
                <td></td>
            </tr>
            <tr>
                <td>2.</td>
                <td>Departemen HSSE</td>
                <td>-</td>
                <td>2</td>
                <td>2</td>
                <td>5</td>
                <td>5</td>
                <td></td>
            </tr>
            <tr class="bold">
                <td></td>
                <td style="text-align: right; font-weight: 700">Sub total I</td>
                <td>0</td>
                <td>5</td>
                <td>3</td>
                <td>8</td>
                <td>20</td>
                <td></td>
            </tr>
            <tr class="bold">
                <td></td>
                <td style="text-align: right; font-weight: 700">Total</td>
                <td>0</td>
                <td>5</td>
                <td>3</td>
                <td>8</td>
                <td>20</td>
                <td>36</td>
            </tr>
        </table>
    </div>

    <script type="text/javascript" src="<?= base_url().'assets/js/jquery-3.6.3.min.js' ?>"></script>

</body>

</html>