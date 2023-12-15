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

  fail += validateFile(form.filename);
  fail += validateKey(form.key.value);
  fail += validateMethod(form.method.value);
  fail += validateSecondKey(form.secondKey.value, form.method.value);

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
    return 'A key is missing. Please enter an alphanumeric string.\n';
  } else {
    return (/^[a-zA-Z0-9]+$/.test(key)) ? '' : 'A key is invalid. Please enter an alphanumeric string.\n';
  }
}

function validateMethod(method) {
  if (method == '') return 'No method was selected. Please select a method (button). \n';
  else {
    const validMethods = ["encryptSubstitution", "encryptTransposition", "decryptSubstitution", "decryptTransposition"];
    return (validMethods.includes(method)) ? '' : 'Method is invalid. Choose a proper method. \n';
  }
}

function validateSecondKey(key, method) {
  // if we're not using double transposition, there's no error...
  const methods = ["encryptTransposition", "decryptTransposition"];
  return (!methods.includes(method)) ? '' : validateKey(key);
}