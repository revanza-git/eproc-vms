<!DOCTYPE html>
<html lang="en">
<head>
    <title>Table Layout</title>
    <style>
        @import url("https://fonts.googleapis.com/css?family=Open+Sans:300,400,700");
        body {
            width: 709px;
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
            border-spacing:none;
        }
        tr th {
            border: 1px solid #a0a0a0;
            border-spacing:none;
        }
        .export {
            background-color: #fff;
            width: 100%;
            margin: 5px 0; }
            .export td, .export th {
            vertical-align: middle;
            text-align: center;
            border-spacing: none;
            padding: 5px; }
            .export th {
            padding: 5px; }
            .export-logo {
            margin: 15px; }
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
            height: 150px;
            padding: 5px; }
    </style>

</head>

<body>
    <table class="export"> 
        <tr> 
            <th colspan="3">
                <div class="export-logo">
                    <img src="'.base_url('/assets/images/NUSANTARA-REGAS-2.png').'"">
                </div>
            </th> 
            <th colspan="4">
                <div class="export-name">
                    formulir perubahan perencanaan pengadaan b/j ("fp3")
                </div>
            </th> 
        </tr> 
    </table>
    <table class="export">
        <tr> 
            <td style="width: 50%;">
                <ul class="export-info">
                    <li>
                        <span>Kepada</span> 
                        <span>:</span> 
                        <span>Kepala Divisi SDM</span>
                    </li>
                    <li>
                        <span>Dari</span> 
                        <span>:</span> 
                        <span>Kepala Divisi SDM</span>
                    </li>
                    <li>
                        <span>Pusat Biaya</span> 
                        <span>:</span> 
                        <span>Kepala Divisi SDM</span>
                    </li>
                </ul>
            </td>
            <td style="vertical-align: top; width: 50%;">
                <ul class="export-info">
                    <li>
                        <span>Nomor</span> 
                        <span>:</span> 
                        <span>Kepala Divisi SDM</span>
                    </li>
                    <li>
                        <span>Tanggal</span> 
                        <span>:</span> 
                        <span>Kepala Divisi SDM</span>
                    </li>
                </ul>
            </td>
        </tr>
    </table>
    <table class="export">
        <tr> 
            <th rowspan="2">No</th> 
            <th rowspan="2">No PR <br> (*apabila ada)</th> 
            <th rowspan="2">
                Nama Pengadaan B/J <br>
                (Sesuai Perencanaan Pengadaan B/J 
                Tahun 2018)
            </th> 
            <th colspan="3">Perubahan Perencanaan</th> 
            <th rowspan="2">Keterangan</th>
        </tr> 
        <tr>
            <td>378</td>
            <td>378</td>
            <td>378</td>
        </tr>
        <tr>
            <td>378</td>
            <td>378</td>
            <td>378</td>
            <td>378</td>
            <td>378</td>
            <td>378</td>
            <td>378</td>
        </tr>
    </table> 
    <table class="export">
        <tr>
            <td colspan="3" rowspan="2">
                Pengguna Barang/Jasa
                (setingkat Ka. Dept)
            </td>
            <td colspan="4">
                Persetujuan Perubahan
            </td>
        </tr>
        <tr>
            <td colspan="4">
                Pengguna Barang/Jasa
                (setingkat Ka. Divisi atau Direktur Utama untuk fungsi leher)
            </td>
        </tr>
        <tr>
            <td colspan="3" class="sign-area" style="width: 50%;">
                
            </td>
            <td colspan="4" class="sign-area" style="width: 50%;">
                
            </td>
        </tr>
        <tr>
            <td colspan="3" style="width: 50%;">(.......................)</td>
            <td colspan="4" style="width: 50%;">(.......................)</td>
        </tr>
    </table>

</body>

</html>