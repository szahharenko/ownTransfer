<?php
	@session_start();
	$lang  = 'en'; //set app language [en,et,ru]
	$sid   = isset($_GET['sid']) ? $_GET['sid'] : false;
	$dev   = isset($_GET['dev']) ? true : false;
	$owner = isset($_GET['owner']) ? $_GET['owner'] : false;
	
	include_once("config.php");
	include_once("translations.php");
	include_once("functions.php");

	define('PACKAGE',$sid);
	define('OWNER',$owner);

	if(PACKAGE) { /* Reset session for new transfer */
		session_unset();
		session_destroy();
		setcookie("PHPSESSID", "", 1);
		session_start();
		session_regenerate_id(true);
	}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" /><![endif]-->
    <meta charset="utf-8" />
    <title><?= APP_TITLE ?></title>
    <meta name="description" content="<?= APP_DESCRIPTION ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.3.1/slate/bootstrap.min.css"> 
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<script src="js/app.js"></script>
	<link rel="stylesheet" href="css/app.css"/>
	<script>var server_url = '<?= SERVER_URL ?>';</script>
  </head>
  <body>
	<?php if(APP_VIDEO): ?>
		<div class="videobg"><video autoplay muted loop><source src="<?= APP_VIDEO ?>" type="video/mp4"></video></div>
	<?php endif; ?>
	<header class="main-header" role="banner">
		<div class="container">
			<div class="row">
				<div class="col-sm-6">
					<a href="<?= APP_URL ?>" id="logo" class="logo">
						<span>
						<?php if(APP_LOGO): ?>
							<img src="<?= APP_LOGO ?>" alt="<?= APP_TITLE ?>"/>
						<?php else: echo APP_TITLE; endif; ?>
						</span>
					</a>
				</div>
				<?php if(!PACKAGE): ?>
				<div class="col-sm-6 stats">
					<strong class="total"><?= count( glob(__DIR__.FILES_PATH."*", GLOB_ONLYDIR) ) ?> <?= $APP_TXT['TRANSFERS'] ?> / <?= GetDirectorySize(__DIR__.FILES_PATH) ?></strong>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</header>
	<div class="container" id="uploader">
		<?php if(!PACKAGE): ?>
			<form id="fileupload" action="<?= SERVER_URL; ?>" method="POST" enctype="multipart/form-data">
				<div class="row fileupload-buttonbar">
					<div class="col-sm-8">
						<span class="btn btn-success fileinput-button"><i class="fa fa-plus"></i><span> <?= $APP_TXT['ADD_FILE'] ?></span><input type="file" name="files[]" multiple /></span>
						<a href="?sid=<?= session_id() ?>&owner=1" class="btn btn-lg btn-warning link-generate" id="linkgen" style="display:none;"><i class="fa fa-cloud"></i><span> <?= $APP_TXT['CREATE_LINK'] ?></span></a>
					</div>
					<div class="col-sm-4"><span class="fileupload-process"></span></div>
				</div>
				<table class="table table-striped"><tbody class="files"></tbody></table>
			</form>
			<script id="template-upload" type="text/x-tmpl">
			  {% for (var i=0, file; file=o.files[i]; i++) { %}
				  <tr class="template-upload">
					  <td>
						  {%=file.name%}
						  <strong class="error text-danger"></strong>
					  </td>
					  <td>
						  <div class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar" style="width:0%;"></div></div>
					  </td>
					  <td style="text-align:right;">
						  {% if (!i) { %}
							  <button class="btn btn-sm btn-warning cancel"><i class="glyphicon glyphicon-ban-circle"></i></button>
						  {% } %}
					  </td>
				  </tr>
			  {% } %}
			</script>
			<script id="template-download" type="text/x-tmpl">
			  {% for (var i=0, file; file=o.files[i]; i++) { %}
				  <tr class="template-download">
					  <td>
						  <span>{%=file.name%}</span>
						  {% if (file.error) { %}
							  <div><span class="label label-danger">Error</span> {%=file.error%}</div>
						  {% } %}
					  </td>
					  <td>
						  <span class="size">{%=o.formatFileSize(file.size)%}</span>
					  </td>
					  <td style="text-align:right;">
						  {% if (file.deleteUrl) { %}
							  <button class="btn btn-danger btn-sm delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
								  <i class="fa fa-trash"></i>
							  </button>
						  {% } else { %}
							  <button class="btn btn-sm btn-warning cancel">
								  <i class="fa fa-ban-circle"></i>
							  </button>
						  {% } %}
					  </td>
				  </tr>
			  {% } %}
			</script>

			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js" integrity="sha384-xBuQ/xzmlsLoJpyjoggmTEz8OWUFM0/RC5BsqQBDX2v5cMvDHcMakNTNrHIW2I5f" crossorigin="anonymous"></script>
			<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

			<script src="js/libs/jquery.ui.widget.js"></script>
			<script src="js/libs/tmpl.min.js"></script>
			<script src="js/libs/load-image.all.min.js"></script>
			<script src="js/libs/canvas-to-blob.min.js"></script>
			<script src="js/libs/jquery.blueimp-gallery.min.js"></script>
			<script src="js/libs/jquery.iframe-transport.js"></script>
			<script src="js/libs/jquery.fileupload.js"></script>
			<script src="js/libs/jquery.fileupload-process.js"></script>
			<script src="js/libs/jquery.fileupload-image.js"></script>
			<script src="js/libs/jquery.fileupload-audio.js"></script>
			<script src="js/libs/jquery.fileupload-video.js"></script>
			<script src="js/libs/jquery.fileupload-validate.js"></script>
			<script src="js/libs/jquery.fileupload-ui.js"></script>
			<script>$(function(){UPLOADER.init()});</script>
		<?php else: ?>
			<?php if(OWNER):
				$pack_url = APP_URL.'?sid='. PACKAGE;
				if(APP_SHORTENER) { /* If url shortener is active */ 
					$shortapi = APP_SHORTENER.'?api=1&url='.urlencode($pack_url);
					$shorturl = file_get_contents($shortapi);
					$obj = json_decode($shorturl);
					$pack_url = $obj->short;
				}
				?>
				<div class="row">
					<div class="col-3 pr-0" style="min-width:300px;">
						<div class="input-group"><input type="text" class="form-control" value="<?= $pack_url ?>" readonly id="url<?= $pack_url ?>"/></div>
					</div>
					<div class="col-2 pr-0">
						<button onclick="UPLOADER.copyUrl('<?= $pack_url ?>')" class="btn btn-primary btn-block"><?= $APP_TXT['COPY_LINK'] ?></button>
					</div>
					<div class="col-4">
						<div id="copy<?= $pack_url ?>" class="alert alert-success" role="alert" style="display:none;"><?= $APP_TXT['COPY_OK'] ?></div>
					</div>
				</div>
			<?php endif; ?>
			<?php
				$PATH  = __DIR__.FILES_PATH.PACKAGE;
				$URL   = APP_URL.'download.php?p='.PACKAGE;
				$ZIP   = APP_URL.'download.php?z=1&p='.PACKAGE;
				$FILES = getListOfFiles($PATH);
				if ($FILES) {
					$dir_stat = stat($PATH);
					$modified = $dir_stat['ctime'];
					$removeAt = $modified + ((KEEP_DAYS+1) * 24 * 60 * 60);
					?>
						<h3 class="remove-date mt-3 mb-3"><?= $APP_TXT['REMOVE_TIME'] ?> <span class="text-warning"><?= date('F j', $removeAt); ?></span></h3>
					<?php
				}
			?>
			<?php if($FILES): ?>
				<table class="table table-striped">
					<tbody class="files">
					  <?php
						foreach($FILES as $file) {
							$ext = pathinfo($PATH.'/'.$file, PATHINFO_EXTENSION);
							$size = intval(filesize($PATH.'/'.$file)) ;
							$size = bytesFormat($size);
							$isdir = is_dir($file);
							if ($ext != 'php' && $ext != '') {
								if (PACKAGE.'.zip' != $file) { /* Exclude server zipped transfer */
									$namehref = str_replace('+', '%2B', $file);
									print("<tr class='file'><td><a href='$URL&f=$namehref' target='_blank'>$file</a></td><td>$size</td><td>$ext </td></tr>");
								}
							}
						}
					  ?>
					</tbody>
				</table>
				<a href="<?= $ZIP ?>" class="btn btn-success"><?= $APP_TXT['AS_ZIP'] ?></a>
			<?php else: ?>
				<div class='removed'><?= $APP_TXT['IS_REMOVED'] ?></div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</body>
</html>
