<!doctype html>
<html lang="en">
<head>
  <title><?=$title?></title>

  <link rel="apple-touch-icon" sizes="180x180" href="<?=$this->linkAsset('apple-touch-icon.png')?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?=$this->linkAsset('favicon-32x32.png')?>">
  <link rel="icon" type="image/png" sizes="16x16" href="<?=$this->linkAsset('favicon-16x16.png')?>">
  <link rel="manifest" href="<?=$this->linkAsset('site.webmanifest')?>">
  <link rel="mask-icon" href="<?=$this->linkAsset('safari-pinned-tab.svg" color="#5bbad5')?>">
  <meta name="msapplication-TileColor" content="#2d89ef">

  <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Holonet project management application" />
  <meta name="author" content="matthias.lantsch@bluewin.ch">
  <meta charset='UTF-8'>
  <meta name="robots" content="no index"/>
  <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-gray-800 font-sans text-gray-100 antialiased">
  <?=$this->include('includes/navi')?>

  <?=$this->renderBlock('content')?>
</body>
</html>
