// this file contains Javascript functions for user input (form) validation
// note: FINAL VALIDATION is still done with PHP b/c we still can't trust validation via Javascript (ex. users can disable JS)

function validateSignup(form) {
  let fail = '';

  fail += validateName(form.name.value);
  fail += validateEmail(form.email.value);
  fail += validatePassword(form.password.value);

  if (fail == "") return true;
  else {
    alert(fail); // return popup (alert) containing form errors  
    return false;
  }
}

function validateLogin(form) {
  let fail = '';

  fail += validateEmail(form.email.value);
  fail += validatePasswordInput(form.password.value);

  if (fail == "") return true;
  else {
    alert(fail); // return popup (alert) containing form errors  
    return false;
  }
}

function validateCipherRequest(form) {
  let fail = '';

  /* I ONLY VALIDATE FILE AND KEY INPUTS --> I don't validate the method because by default, one of the methods is selected; thus, no matter what, the user is forced to select a method, so I don't need to check whether or not a method has been picked because we are guaranteed that a method is picked
        --> basically, users can't "uncheck" a method, so since one of the methods is checked by default, there will always be a method selected */
  fail += validateFile(form.filename);
  fail += validateKey(form.key.value);

  if (fail == "") return true;
  else {
    alert(fail); // return popup (alert) containing form errors  
    return false;
  }
}

function validateName(name) {
  return (name == '') ? 'No name entered.\n' : '';
}

function validateEmail(email) {
  if (email == "") return "No email was entered.\n"
  else if (!((email.indexOf(".") > 0) && (email.indexOf("@") > 0)) || /[^a-zA-Z0-9.@_-]/.test(email)) return "The email address is invalid.\n"
  return ""
}
// used in loginPage --> I differentiated this from signupPage's validatePassword because I think we shouldn't share password requirements on a login; users should know their password passes requirements when they signed up
function validatePasswordInput(password) {
  return (password == '') ? 'No password was entered.\n' : '';
}

function validatePassword(password) {
  // I just made up some password requirements (based on the class slides...)
  if (password == '') {
    return 'No password entered.\n';
  } else if (password.length < 6) {
    return 'Password must be at least 6 characters.\n';
  } else if (!/[a-z]/.test(password) || !/[A-Z]/.test(password) || !/[0-9]/.test(password)) {
    return 'Password must contain at least one lowercase letter, one uppercase letter, and one number.\n';
  } else {
    return '';
  }
}

function validateFile(file) {
  const allowedExtensions = /(\.txt)$/i; // I used the internet to help me with this
  // validate file upload + extension type
  if (file.files.length === 0) {
    return "No file was uploaded. Please upload a text file.\n";
  } else if (!allowedExtensions.exec(file.value)) {
    return file.value + " is not an accepted input.\n Please upload a text file.\n";
  } else {
    return '';
  }
}

function validateKey(key) {
  if (key === '') {
    return 'No key was entered. Please enter an alphanumeric string.\n';
  } else {
    let alphanumericRegex = /^[a-zA-Z0-9]+$/;
    return (alphanumericRegex.test(key)) ? '' : 'Key is invalid. Please enter an alphanumeric string.\n';
  }
}