function startUp(){
    document.getElementById("AddEventForm").style.display = "none";
}
function logIn(){
    //checks for valid login
    console.log("LogIn Clicked");
    //document.getElementById("LogInForm").style.display = "none";
    //document.getElementById("AddEventForm").style.display = "block";
}

document.addEventListener("DOMContentLoaded", startUp, false);
const logInButton = document.getElementById("logInButton");
logInButton.addEventListener("clicked", logIn, false);