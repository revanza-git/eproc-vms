<div class="container">

<div class="col col-12">

  <div class="container-title">

    <h1>Layout File</h1>

    <div class="group-button-start">

      Menampilkan 

      <select>

        <option value="">25</option>

        <option value="">50</option>

        <option value="">75</option>

        <option value="">100</option>

      </select>

      Entry

    </div>

    <div class="group-button-end">

      <button class="button is-primary is-wide has-dropdown">Add</button>

      <ul class="dropdown-menu is-dropdown">
        <li>
          <a href="">New file</a>
        </li>
        <li>
          <a href="">New Akta</a>
        </li>
        <li>
          <a href="">New Vendor</a>
        </li>
      </ul>

      <button onclick="switchView('box')" class="button is-rounded"><span class="icon"><i class="fas fa-th"></i></span></button>

      <button onclick="switchView('list')" class="button is-rounded"><span class="icon"><i class="fas fa-list"></i></span></button>

    </div>

  </div>

</div>

<ul>

  <li class="col-4">

    <div class="view view-list">

      <div class="view-item" oncontextmenu="return false;">

        <img src="<?php echo base_url('assets/images/img/folder.png')?>" alt="">

        <img src="<?php echo base_url('assets/images/img/down-arrow-inside-a-circle.png')?>" alt="">

        <div class="caption">

          <p>Title Here</p>

          <p>Caption Here</p>
          
        </div>

      </div>

    </div>

  </li>

  <?php

    for($i = 1; $i <= 10; $i++) {

  ?>

  <li class="col-4">

    <div class="view view-list">

      <div class="view-item" oncontextmenu="return false;">

        <img src="../source/img/folder.png" alt="">

        <img src="../source/img/down-arrow-inside-a-circle.png" alt="">

        <div class="caption">

          <p>Title Here</p>

          <p>Caption Here</p>
          
        </div>

      </div>

    </div>

  </li>

  <?php 

    }

  ?>

</ul>

<div class="container-footer">

  <ul class="pagination">

    <li class="disabled">

      <a href="">

        <span class="icon"><i class="fas fa-arrow-left"></i></span>

      </a>

    </li>

    <li><a href="">1</a></li>

    <li><a href="">2</a></li>

    <li><a href="">3</a></li>

    <li><a href="">4</a></li>

    <li><a href="">5</a></li>

    <li>

      <a href="">

        <span class="icon"><i class="fas fa-arrow-right"></i></span>

      </a>

    </li>

  </ul>

</div>

</div>