<?php
### this code will work only with /register.php ###

$_SESSION['step1_completed'] = false;
// start -- registeration errors array
$errors = [
   "All fields must be filled out!",
   "Incorrect email address!",
   "Passwords don't match!",
   "Password has to be at least 8 characters long!",
   "Repeated password has to be at least 8 characters long!",
   "Email address has to be at least 8 characters long!",
   "Incorrect gender!",
   "Incorrect first name! (min: 2 max: 32 characters)",
   "Incorrect last name! (min: 2 max: 32 characters)",
   "Name can contain only letters!",
   "Email address already taken!"
];
// end -- registeration errors array

// start -- register step1 validation
if (isset($_POST['registerS1']) && isset($_POST['firstname']) && isset($_POST['lastname']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['rpassword']) && isset($_POST['gender'])) {
   if (empty($_POST['firstname']) || empty($_POST['lastname']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['rpassword']) || empty($_POST['gender'])) {
      header("Location: register.php?step=1&error=1"); #1 = All fields must be filled out!
      exit();
   }

   // start -- check post variables
   $firstname = Security::check($_POST['firstname']);
   $lastname = Security::check($_POST['lastname']);
   $email = Security::check($_POST['email']);
   $password = Security::check($_POST['password']);
   $rpassword = Security::check($_POST['rpassword']);
   $gender = Security::check($_POST['gender']);
   // end -- check post variables
   if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      header("Location: register.php?step=1&error=2"); #2 = Incorrect email address!
      exit();
   }else if (DB::query('SELECT user_email FROM users WHERE user_email = :email', [':email' => $email])[0]['user_email']) {
      header("Location: register.php?step=1&error=11"); #11 = Email address already taken!
      exit();
   }else if ($password != $rpassword) {
      header("Location: register.php?step=1&error=3"); #3 = Passwords don't match!
      exit();
   }else if ((strlen($password) <= 8) || (strlen($password) >= 64)) {
      header("Location: register.php?step=1&error=4"); #4 = Password has to be at least 8 characters long!
      exit();
   }else if ((strlen($rpassword) <= 8) || (strlen($rpassword) >= 64)) {
      header("Location: register.php?step=1&error=5"); #5 = Repeated password has to be at least 8 characters long!
      exit();
   }else if ((strlen($email) <= 8) || (strlen($email) >= 64)) {
      header("Location: register.php?step=1&error=6"); #6 = Email address has to be at least 8 characters long!
      exit();
   }else if ($gender != "male" && $gender != "female") {
      header("Location: register.php?step=1&error=7"); #7 = Incorrect gender!
      exit();
   }else if ((strlen($firstname) <= 2) || (strlen($firstname) >= 32)) {
      header("Location: register.php?step=1&error=8"); #8 = Incorrect first name! (min: 2 max: 32 characters)
      exit();
   }else if ((strlen($lastname) <= 2) || (strlen($lastname) >= 32)) {
      header("Location: register.php?step=1&error=9"); #9 = Incorrect last name! (min: 2 max: 32 characters)
      exit();
   }else if (!preg_match("/^[a-zA-Z]*$/", $firstname) && !preg_match("/^[a-zA-Z]*$/", $lastname)) {
      header("Location: register.php?step=1&error=10"); #10 = Name can contain only letters!
      exit();
   }

   // start -- validation with js & php succeeded and now data'll be saved in session's variables

   $_SESSION['firstname'] = ucfirst($firstname);
   $_SESSION['lastname'] = ucfirst($lastname);
   $_SESSION['name'] = ucfirst($firstname . " " . $lastname);
   $_SESSION['email'] = $email;
   $_SESSION['password'] = password_hash($password, PASSWORD_DEFAULT);
   $_SESSION['gender'] = $gender;
   $_SESSION['birthday'] = "0"; # TODO: add birthday script

   if ($gender == "male") {
      $_SESSION['sex'] = "m";
   }else if ($gender == "female") {
      $_SESSION['sex'] = "f";
   }else {
      $_SESSION['sex'] = "m";
   }

   $_SESSION['step1_completed'] = true;
   $_SESSION['step'] = 2; # step 1 is now completed, so we update `step` variable to 2.

   header("Location: register.php?step=2");
   exit();

   // end -- validation with js & php succeeded and now data'll be saved in session's variables
}
// end -- register step1 validation
?>
<!DOCTYPE html><html lang="<?= $app->lang ?>"><head><?php require_once('app/incs/head-metas.inc.php'); ?><title>Register</title></head><body><div id="registerS1"><h1>Register</h1>
   <div id="errors"><?php
      if (isset($_GET['error'])) {
         $error = htmlspecialchars(trim($_GET['error']));
              if ($error == 1)  { $error = $errors[0];  }
         else if ($error == 2)  { $error = $errors[1];  }
         else if ($error == 3)  { $error = $errors[2];  }
         else if ($error == 4)  { $error = $errors[3];  }
         else if ($error == 5)  { $error = $errors[4];  }
         else if ($error == 6)  { $error = $errors[5];  }
         else if ($error == 7)  { $error = $errors[6];  }
         else if ($error == 8)  { $error = $errors[7];  }
         else if ($error == 9)  { $error = $errors[8];  }
         else if ($error == 10) { $error = $errors[9];  }
         else if ($error == 11) { $error = $errors[10]; }

         echo $error;
      }
   ?></div>
   <form action="register.php?step=1" method="post" onsubmit="return validateRegisterS1()" name="registerS1Form">
      <div>
         <label for="firstname">first name: </label><input type="text" name="firstname" value="" id="firstname" pattern="[a-zA-ZąćęłńóśźżĄĘŁŃÓŚŹŻ]+" title="First name can contain only letters!">
      </div>
      <div>
         <label for="lastname">last name: </label><input type="text" name="lastname" value="" id="lastname" pattern="[a-zA-ZąćęłńóśźżĄĘŁŃÓŚŹŻ]+" title="Last name can contain only letters!">
      </div>
      <div>
         <label for="email">email: </label><input type="text" name="email" value="" id="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$" title="Incorrect email address!">
      </div>
      <div>
         <label for="password">password: </label><input type="password" name="password" value="" id="password">
      </div>
      <div>
         <label for="rpassword">repeat password: </label><input type="password" name="rpassword" value="" id="rpassword">
      </div>
      <div>
         <input type="radio" name="gender" value="male" checked> Male <input type="radio" name="gender" value="female"> Female
      </div>
      <div><button type="submit" name="registerS1">register</button></div>
   </form>
   <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
   <script src="assets/registerS1.js"></script>
</div></body></html>
