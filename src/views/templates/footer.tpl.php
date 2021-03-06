  </div>
  <link rel="stylesheet" href="//cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
  <link rel="icon" type="image/ico"  href="<?=linkAsset('favicon.ico')?>">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"
          integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js"
          integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
  <script src="//cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
  <script src='<?=linkJs("common")?>'></script>
  <script src="<?=linkJs("formular")?>"></script>
  <nav class="navbar fixed-bottom navbar-light bg-faded">
    <div class="container text-muted">
      <div class="pull-left">
        &copy; <?=date('Y')?> Matthias Lantsch |
        <a target="_blank" href="https://icons8.de/icons/set/git">Git icon</a> icon by <a target="_blank" href="https://icons8.de">Icons8</a>
      </div>
      <div class="pull-right">
        HGIT <?=$appVersion?>
      </div>
    </div>
  </nav>
  </body>
</html>
