<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once 'config.php';

use Google\Cloud\Datastore\DatastoreClient;
use Google\Cloud\Storage\StorageClient;

$projectId = getenv("MY_PROJECT_ID");
$bucketstorage = getenv("MY_BUCKET_ID");

$datastore = new DatastoreClient([
	'projectId' => $projectId]);

$query = $datastore->query()
	->kind('game');

if(isset($_POST['View']))
{
	$_SESSION['viewItem'] = htmlspecialchars($_POST['View']);
	$key = $datastore->key('game', htmlspecialchars($_SESSION['viewItem']));
	
	$item = $datastore->lookup($key);

	$_SESSION['seller'] = $item['user'];
	echo "<script type='text/javascript'>
		window.location.href='/product.html';
		</script>";
	exit();
}

if (isset($_GET['platform']))
{
	if ($_GET['platform'])
	{
		$query = $query->filter('platform', '=', $_GET['platform']);
	}
}

if (isset($_GET['search']))
{
	$result = $datastore->runQuery($query);
	$arr_result = iterator_to_array($result);
}
?>

<?foreach(array_reverse($arr_result) as $display) { ?>
	<div class='panel panel-default'>
		<div class='panel-body' style='text-align:left'>
			<div class='col-sm-2'>
				<img src="<?php echo "https://storage.cloud.google.com/".$bucketstorage."/".$display['user']."/".$display['accessID'].".jpg"; ?>" width='60' height='80'>
			</div>

			<div class='col-sm-8' style='text-align:left'>
				<div>Title: <?php echo $display['title'];?></div>
				<div>Transaction method: <?php echo $display['transaction'] ?></div>
				<div>Platform: <?php echo $display['platform']; ?></div>
				<div>Date posted: <?php echo $display['date']->format('Y-m-d h:i'); ?></div>
				<div>Posted by: <?php echo $display['user'] ?></div>

				<?php if($display['transaction'] != "Trade only") { ?>
					<div>Price: $<?php echo $display['price']; ?></div>
				<?php } ?>
			</div>

			<div class="col-sm-2" style="text-align:center">
				<form method='post'>
					<button type='submit' value="<?php echo $display['accessID']; ?>" name='View'>
						View
					</button>
				</form>
			</div>
		</div>
	</div>

<?php } ?>