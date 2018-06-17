<input type="hidden" id="idRepo" value="<?=$project->id?>"/>
<input type="hidden" id="branchTf" value="<?=$refspec?>"/>
<h3>Git viewing
  <a href="<?=linkTo("{$project->slugname()}")?>"><?=$project->name?></a> /
  <a href="<?=linkTo("{$project->slugname()}/git/tree/{$refspec}")?>"><?=$refspec?></a>
  <?php if(strlen($path) > 0): ?>
    / <?=$path?>
  <?php endif;?>
</h3>
<ul class="nav nav-pills nav-justified">
  <li class="nav-item">
    <a class="nav-link <?=($page == "tree" || $page == "blob" ? "active" : "")?>"
      href="<?=linkTo("{$project->slugname()}/git/tree/{$branch}")?>">Files</a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?=($page == "commit" || $page == "log" ? "active" : "")?>"
      href="<?=linkTo("{$project->slugname()}/git/log/{$branch}")?>">Commits</a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?=($page == "tags" ? "active" : "")?>"
      href="<?=linkTo("{$project->slugname()}/git/tags/{$branch}")?>">Tags</a>
  </li>
</ul>
<hr>
<div class="row">
  <div class="col-lg-6">
    <div class="input-group">
      <span class="input-group-addon" id="filter-addon">Filter</span>
      <input type="text" id="filterTf" class="form-control"
        placeholder="Type to filter" aria-describedby="filter-addon">
    </div>
  </div>
  <div class="col-lg-6">
    <div class="input-group">
      <input type="text" id="copyFrom" value="https://<?=$_SERVER["HTTP_HOST"].linkTo("{$project->slugname()}/repo/"."{$project->slugname()}.git")?>"
        class="form-control" placeholder="webgit clone url">
      <span class="input-group-btn">
        <button class="btn btn-default copyButton" type="button"><i class="fa fa-files-o" aria-hidden="true"></i></button>
      </span>
    </div>
  </div>
</div>
<hr>
<div class="row">
  <div class="col-md-10">
