<?php 

require_once "koneksi.php";

if( !isset($_SESSION['user']) ){
	header("Location: index.php");
}

if( $_SESSION['level'] != "Admin" ){
	header("Location: index.php");
}

// Filters
$start = isset($_GET['start']) ? mysqli_escape_string($connect, $_GET['start']) : '';
$end = isset($_GET['end']) ? mysqli_escape_string($connect, $_GET['end']) : '';
$status = isset($_GET['status']) ? mysqli_escape_string($connect, $_GET['status']) : 'Lunas';

$where = [];
if($status !== ''){ $where[] = "p.status='".$status."'"; }
if($start !== '' && $end !== ''){ $where[] = "p.tglBayar BETWEEN '".$start."' AND '".$end."'"; }
$whereSql = count($where) ? ("WHERE ".implode(' AND ', $where)) : '';

$sql = mysqli_query($connect, "SELECT p.*, t.noTagihan, t.noPelanggan, c.namaLengkap FROM tbpembayaran p JOIN tbtagihan t ON t.kodeTagihan=p.kodeTagihan JOIN tbpelanggan c ON c.noPelanggan=t.noPelanggan $whereSql ORDER BY p.tglBayar DESC");

// Totals
$totals = mysqli_query($connect, "SELECT COUNT(*) as cnt, COALESCE(SUM(p.jumlahTagihan),0) as total FROM tbpembayaran p $whereSql");
$agg = ['cnt'=>0,'total'=>0];
if($totals && mysqli_num_rows($totals)>0){ $agg = mysqli_fetch_assoc($totals); }

?>
<!DOCTYPE html>
<html>
<head>
	<title> Paytrik - Rekap Penjualan </title>
	<meta name="viewport" content="width=device-width">
	<link rel="stylesheet" type="text/css" href="style.css">
	<style>
		@media print {
			.menu, .btn-logout, .btn-print, form, .head { display: none !important; }
			.table-print { width: 100%; }
		}
	</style>
</head>
<body>
<div class="container bflex">
	<div class="head cflex">
		<a class="btn-menu">
			<div class="icon icon1"></div>
			<div class="icon icon2"></div>
			<div class="icon icon3"></div>
		</a>
	</div>
	<div class="lcontainer">
		<ul class="menu">
			<li class="title-menu"><a href="dashboard.php"><span> Dashboard </span></a></li>
			<?php if( $_SESSION['level'] == 'Admin' ){ ?>
				<li class="title-menu"><a href="inputtarif.php"><span> Input </span></a>
					<ul class="drop">
						<li><a href="inputtarif.php"> Tarif </a></li>
						<li><a href="inputpetugas.php"> Petugas </a></li>
						<li><a href="inputpelanggan.php"> Pelanggan </a></li>
					</ul>
				</li>
				<li class="title-menu"><a href="tampiltarif.php"><span> Tampil </span></a>
					<ul class="drop">
						<li><a href="tampiltarif.php"> Tarif </a></li>
						<li><a href="tampilpetugas.php"> Petugas </a></li>
						<li><a href="tampilpelanggan.php"> Pelanggan </a></li>
					</ul>
				</li>
			<?php } ?>
			<li class="title-menu"><a href="inputtagihan.php"><span> Tagihan </span></a></li>
			<li class="title-menu"><a href="inputpembayaran.php"><span> Pembayaran </span></a></li>
			<li><a class="btn-logout"><span> Logout </span></a></li>
		</ul>
	</div>
	<div class="rcontainer">
		<div class="header bflex">
			<a href="dashboard.php">
				<img src="image/logo.png">
			</a>
			<div class="title-user">
				<span> <?php echo $_SESSION['level'] ?> </span>
			</div>
		</div>
		<div class="wrapper">
			<div class="logout">
				<div class="screen-logout"></div>
				<div class="delimiter">
					<div class="clogout">
						<img src="export/logout.png">
						<h1> Apakah anda ingin Logout ? </h1>
						<p> Tekan tombol logout untuk keluar dari halaman atau tekan tombol batalkan untuk membaltakannya. </p>
						<div class="cflex">
							<a href="logout.php" class="btn-hapus"> Logout </a>
							<a class="close-logout"> Batalkan </a>
						</div>
					</div>
				</div>
			</div>
			<div class="delimiter">
				<div class="page">
					<div class="bflex move">
						<h1> Rekap Penjualan </h1>
						<div class="cross cflex">
							<a href="javascript:;" class="btn-edit btn-print" onclick="window.print()"> Cetak </a>
							<p> Cetak laporan </p>
						</div>
					</div>
				</div>
				<form method="GET" class="bflex" style="gap:10px; margin: 10px 0 20px 0;">
					<div class="cinput">
						<span> Dari </span>
						<input type="date" name="start" value="<?php echo htmlspecialchars($start); ?>">
					</div>
					<div class="cinput">
						<span> Sampai </span>
						<input type="date" name="end" value="<?php echo htmlspecialchars($end); ?>">
					</div>
					<div class="cinput">
						<span> Status </span>
						<select name="status">
							<option value=""> Semua </option>
							<option value="Lunas" <?php echo $status=='Lunas'? 'selected':''; ?>> Lunas </option>
							<option value="Proses" <?php echo $status=='Proses'? 'selected':''; ?>> Proses </option>
							<option value="Belum" <?php echo $status=='Belum'? 'selected':''; ?>> Belum </option>
						</select>
					</div>
					<div class="cinput" style="align-self: flex-end;">
						<button type="submit"> Tampilkan </button>
					</div>
				</form>
				<div class="wmax max table-print">
					<table>
						<tr>
							<td><span> No </span></td>
							<td><span> Tanggal </span></td>
							<td><span> No Tagihan </span></td>
							<td><span> No Pelanggan </span></td>
							<td><span> Nama </span></td>
							<td><span> Jumlah </span></td>
							<td><span> Status </span></td>
						</tr>
						<?php 
							$no=1; 
							if($sql && mysqli_num_rows($sql)>0){
								while($row = mysqli_fetch_assoc($sql)){
						?>
						<tr>
							<td><p class="number"> <?php echo $no++; ?> </p></td>
							<td><p> <?php echo $row['tglBayar']; ?> </p></td>
							<td><p> <?php echo $row['noTagihan']; ?> </p></td>
							<td><p> <?php echo $row['noPelanggan']; ?> </p></td>
							<td><p> <?php echo $row['namaLengkap']; ?> </p></td>
							<td><p> <?php echo number_format($row['jumlahTagihan'],0,',','.'); ?> </p></td>
							<td><p> <?php echo $row['status']; ?> </p></td>
						</tr>
						<?php 
							}
						} else { 
							echo "<script>
								var error = document.getElementsByClassName('error')[0];
								error && (error.style.display='block');
								error && (error.innerHTML = 'Data rekap kosong.');
							</script>";
						}
						?>
					</table>
				</div>
				<div class="sflex" style="justify-content: space-between; margin-top: 15px;">
					<p>Total Transaksi: <strong><?php echo (int)$agg['cnt']; ?></strong></p>
					<p>Total Penjualan: <strong>Rp <?php echo number_format($agg['total'],0,',','.'); ?></strong></p>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="script.js"></script>
</body>
<?php include 'footer.php'; ?>
</html>
