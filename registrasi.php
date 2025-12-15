<?php 

require_once "koneksi.php";

if( isset($_SESSION['user']) ){
	header("Location: dashboard.php");
}

?>
<!DOCTYPE html>
<html>
<head>
	<title> Paytrik - Registrasi Pelanggan </title>
	<meta name="viewport" content="width=device-width">
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<div class="login bflex">
	<div class="llogin">
		<img src="image/login.png">
	</div>
	<div class="rlogin cflex">
		<form method="POST">
			<p class="error"></p>
			<img src="image/logo.png">
			<div class="winput">
				<input type="text" name="noPelanggan" autocomplete="off" placeholder="No Pelanggan (username) . . .">
			</div>
			<div class="winput" style="margin-top: 15px">
				<input type="password" name="password" autocomplete="off" placeholder="Password . . .">
			</div>
			<div class="winput bflex" style="margin-top: 15px">
				<div class="cinput rmargin">
					<span> No Meter </span>
					<input type="text" name="noMeter" autocomplete="off" placeholder="Masukan no meter . . .">
				</div>
				<div class="cinput">
					<span> Tarif </span>
					<select name="kodeTarif">
						<?php 
							$sql = mysqli_query($connect, "SELECT * FROM tbtarif");
							while( $data = mysqli_fetch_assoc($sql) ){
						?>
							<option value="<?php echo $data['kodeTarif'] ?>"> <?php echo $data['daya'] ?> ( Tarif/Kwh : <?php echo $data['tarifPerKwh'] ?> ) </option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="winput" style="margin-top: 15px">
				<input type="text" name="namaLengkap" autocomplete="off" placeholder="Nama Lengkap . . .">
			</div>
			<div class="winput" style="margin-top: 15px">
				<input type="text" name="telp" autocomplete="off" placeholder="Telepon . . .">
			</div>
			<div class="winput" style="margin-top: 15px">
				<textarea name="alamat" placeholder="Alamat . . ."></textarea>
			</div>
			<div class="winput" style="margin-top: 25px;">
				<button type="submit" name="btn-register" style="width: 100%;"> Daftar </button>
			</div>
			<p style="margin-top: 10px;"> Sudah punya akun? <a href="index.php">Login</a> </p>
		</form>
	</div>
</div>
</body>
</html>
<?php 

if( isset($_POST['btn-register']) ){
	$noPelanggan = mysqli_escape_string($connect, htmlspecialchars($_POST['noPelanggan']));
	$password = mysqli_escape_string($connect, htmlspecialchars($_POST['password']));
	$noMeter = mysqli_escape_string($connect, htmlspecialchars($_POST['noMeter']));
	$kodeTarif = mysqli_escape_string($connect, htmlspecialchars($_POST['kodeTarif']));
	$namaLengkap = mysqli_escape_string($connect, htmlspecialchars($_POST['namaLengkap']));
	$telp = mysqli_escape_string($connect, htmlspecialchars($_POST['telp']));
	$alamat = mysqli_escape_string($connect, htmlspecialchars($_POST['alamat']));

	if( !empty(trim($noPelanggan)) && !empty(trim($password)) && !empty(trim($noMeter)) && !empty(trim($namaLengkap)) && !empty(trim($telp)) && !empty(trim($alamat)) ){

		$exists1 = mysqli_query($connect, "SELECT username FROM tblogin WHERE username='$noPelanggan'");
		$exists2 = mysqli_query($connect, "SHOW TABLES LIKE 'tbpendaftaran'");
		$existsPending = false;
		if( mysqli_num_rows($exists2) > 0 ){
			$pending = mysqli_query($connect, "SELECT id FROM tbpendaftaran WHERE noPelanggan='$noPelanggan' AND status='Menunggu'");
			$existsPending = mysqli_num_rows($pending) > 0;
		}

		if( mysqli_num_rows($exists1) > 0 || $existsPending ){
			echo "<script>
				var error = document.getElementsByClassName('error')[0];
				error.style.display='block';
				error.innerHTML = 'No Pelanggan sudah digunakan atau masih menunggu validasi!';
			</script>";
		} else {
			// Ensure registration table exists (soft-fail if not)
			$create = mysqli_query($connect, "CREATE TABLE IF NOT EXISTS tbpendaftaran (id INT AUTO_INCREMENT PRIMARY KEY, noPelanggan VARCHAR(50) NOT NULL, password VARCHAR(50) NOT NULL, noMeter VARCHAR(50) NOT NULL, kodeTarif INT NOT NULL, namaLengkap VARCHAR(50) NOT NULL, telp VARCHAR(15) NOT NULL, alamat VARCHAR(100) NOT NULL, status VARCHAR(15) NOT NULL DEFAULT 'Menunggu', createdAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP)");
			if($create){
				$noMeterPref = 'PJYN'.$noMeter;
				$ins = mysqli_query($connect, "INSERT INTO tbpendaftaran (noPelanggan, password, noMeter, kodeTarif, namaLengkap, telp, alamat, status) VALUES('$noPelanggan', '$password', '$noMeterPref', '$kodeTarif', '$namaLengkap', '$telp', '$alamat', 'Menunggu')");
				if($ins){
					echo "<script>alert('Pendaftaran berhasil. Menunggu validasi admin.');location.href='index.php';</script>";
				} else {
					echo "<script>
						var error = document.getElementsByClassName('error')[0];
						error.style.display='block';
						error.innerHTML = 'Gagal menyimpan pendaftaran!';
					</script>";
				}
			} else {
				echo "<script>
					var error = document.getElementsByClassName('error')[0];
					error.style.display='block';
					error.innerHTML = 'Gagal menyiapkan tabel pendaftaran!';
				</script>";
			}
		}
	} else {
		echo "<script>
			var error = document.getElementsByClassName('error')[0];
			error.style.display='block';
			error.innerHTML = 'Form masih ada yang kosong!';
		</script>";
	}
}

?>
