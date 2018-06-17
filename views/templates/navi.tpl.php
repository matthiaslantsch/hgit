<nav class="navbar fixed-top navbar-toggleable-md navbar-inverse bg-inverse mb-4">
  <div class="container">
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
      <div class="navbar-nav mr-auto">
        <a class="nav-item navbar-brand" href="<?=linkTo()?>">HGIT</a>
        <span class="nav-item text-muted navbar-brand"><?=$title?></span>
      </div>
      <div class="alertArea align-middle mx-auto"></div>
      <div class="navbar-nav">
        <?php if(isset($project)): ?>
          <!--
            <li class="nav-item"><a class="nav-link" href="<?=linkTo("{$project->slugname()}")?>"><i class="fa fa-feed"></i> Activity</a></li>
            <?php if(isAllowedAction($project, "readFiles")): ?>
              <li class="nav-item"><a class="nav-link" href="<?=linkTo("{$project->slugname()}/dir")?>"><i class="fa fa-folder-open-o"></i> Project Files</a></li>
            <?php endif; ?>
          -->
          <?php if(isAllowedAction($project, "readCode")): ?>
            <a class="nav-link nav-item" href="<?=linkTo("{$project->slugname()}/git")?>"><i class="fa fa-code-fork"></i> Git Repository</a>
          <!--<li class="nav-item"><a class="nav-link" href="<?=linkTo("{$project->slugname()}/issues")?>"><i class="fa fa-tasks"></i> Issues</a></li>-->
          <?php endif; ?>
          <!--
            <?php if(isAllowedAction($project, "readWiki")): ?>
              <li class="nav-item"><a class="nav-link" href="<?=linkTo("{$project->slugname()}/wiki")?>"><i class="fa fa-comments"></i>Wiki</a></li>
            <?php endif; ?>
            <?php if(isAllowedAction($project, "downloadArtifacts")): ?>
              <li class="nav-item"><a class="nav-link" href="<?=linkTo("{$project->slugname()}/f")?>"><i class="fa fa-download"></i> Downloads</a></li>
            <?php endif; ?>
          -->
        <?php else: ?>
          <!--<li class="nav-item"><a class="nav-link" href="<?=linkTo()?>"><i class="fa fa-tachometer"></i> Dashboard</a></li>-->
          <a class="nav-link nav-item" href="<?=linkTo()?>"><i class="fa fa-list"></i> Projects</a>
          <!--
            <?php if(isset($session_user)): ?>
              <li class="nav-item"><a class="nav-link" href="<?=linkTo("dir")?>"><i class="fa fa-folder-open-o"></i> Project Directory</a></li>
            <?php endif; ?>
            <li class="nav-item"><a class="nav-link" href="<?=linkTo("help")?>"><i class="fa fa-question-circle-o"></i> Help</a></li>
          -->
        <?php endif; ?>
        <?php if(isset($session_user)): ?>
          <div class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
              <i class="fa fa-user fa-fw"></i>
            </a>
            <div class="dropdown-menu">
              <span class="dropdown-header" href="#"><?=$session_user->username?></span>
              <a class="dropdown-item" href="<?=linkTo("user")?>"><i class="fa fa-info-circle"></i> User Profile</a>
              <!--<a class="dropdown-item" href="<?=linkTo("user/settings")?>"><i class="fa fa-gears fa-fw"></i> Settings</a>-->
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="<?=linkTo("logout")?>"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
            </div>
          </div>
        <?php else: ?>
          <a class="nav-link" href="<?=linkTo("login")?>"><i class="fa fa-sign-in"></i> Login</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
<div class="container">
