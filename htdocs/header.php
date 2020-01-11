<nav class="navbar navbar-expand-lg bg-dark navbar-dark">
  <div class="container">    
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <a href="./" class="navbar-brand text-light">Futura Images</a>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <!-- <ul class="navbar-nav">
        <li class="nav-item active dropdown">
          <a class="nav-link dropdown-toggle <?php if(@$_SESSION['login'] !== true){ echo 'disabled';} ?>" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Praktikan
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="?go=tata-tertib">Tata tertib</a>
          </div>
        </li>
      </ul> -->
      <!-- <ul class="navbar-nav ml-auto">
        <li class="nav-item active dropdown">
          <a class="nav-link dropdown-toggle <?php if(@$_SESSION['login'] !== true){ echo 'disabled';} ?>" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?php  
          if(@$_SESSION['login']){
            echo $_SESSION['namalengkap']." (".$_SESSION['npm'].") - PC-".$_SESSION['nopc'];
          } else {
            echo "Anda belum login";
          }
        ?>
          </a>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="?go=profil">Profil</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="?get=logout">Keluar</a>
          </div>
        </li>
      </ul> -->
    </div>
  </div>
</nav>