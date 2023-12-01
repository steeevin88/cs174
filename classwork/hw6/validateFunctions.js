// this file contains Javascript functions for user input (form) validation
// note: FINAL VALIDATION is still done with PHP b/c we still can't trust validation via Javascript (ex. users can disable JS)

function validateSignup(form) {
  let fail = '';

  fail += validateName(form.name.value);
  fail += validateID(form.id.value);
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

function validateSearch(form) {
  let fail = '';

  fail += validateName(form.name.value);
  fail += validateID(form.id.value);

  if (fail == "") return true;
  else {
    alert(fail); // return popup (alert) containing form errors  
    return false;
  }
}

function validateName(name) {
  return (name == '') ? 'No name entered.\n' : '';
}

function validateID(id) {
  // I didn't use isNaN because ID inputs are Strings still, so an empty input would pass isNaN
  return (id == '') ? 'No ID entered.\n' : '';
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