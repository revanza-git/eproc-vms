<div class="sidebar">

<ul class="sidebar-wrapper">
  <li class="sidebar-list">
    <a href="<?= base_url().'template_test/show/beranda' ?>">Beranda</a>
    <span class="badge is-primary">New</span>
  </li>
  <li class="sidebar-list">
    <a href="<?= base_url().'template_test/show/akta' ?>">akta</a>
    <span class="badge is-primary">New</span>
  </li>
  <li class="sidebar-list">
    <a href="<?= base_url().'template_test/show/auction' ?>">auction</a>
    <span class="badge is-primary">New</span>
  </li>
  <li class="sidebar-list">
    <a href="<?= base_url().'template_test/show/chart' ?>">chart</a>
    <span class="badge is-primary">New</span>
  </li>

 <?php 
    $name = ["beranda", "akta", "domisili perusahaan", "TDP", "pengurus perusahaan", "pemilik modal", "izin usaha", "pengalaman", "pabrikan / keagenan / distributor", "aspek k3", "auction"]; 
    $length = count($name);
    for ($i = 0; $i < $length; $i++) {
  ?>

  <!-- <li class="sidebar-list">
    
    <a href="<?= $name[$i] ?>.php" class="sidebar-heading">
      
    </a>

  </li> -->

  <?php 
    }     
  ?>

</ul>

</div>