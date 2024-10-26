const dayDivs = document.getElementsByClassName("day");
const monthLabel = document.getElementById("monthLabel");
const logInButton = document.getElementById("logInButton");
let currDate = new Date();

function daysInMonth(month, year){
    let newDate = new Date(year, month+1, 0);
    return newDate.getDate();
}

function setUpCalendar(){
    monthLabel.innerHTML = mString(currDate) + " " + currDate.getFullYear();
    let firstDayOfMonth = new Date(currDate.getFullYear(), currDate.getMonth(), 1).getDay();
    let maxDays = daysInMonth(currDate.getMonth(), currDate.getFullYear());
    //console.log(firstDayOfMonth+maxDays);
    for(let i = 0; i<dayDivs.length; i++){
        if(i<firstDayOfMonth){
            dayDivs[i].innerHTML = "";
        }
        else if(i<firstDayOfMonth+maxDays){
            dayDivs[i].innerHTML = i-firstDayOfMonth+1;
        }
        else{
            dayDivs[i].innerHTML = "";
        }
    }
}

function mString(dateO){
    return dateO.toLocaleString('default',{month: 'long'});
}

function nextMonth(){
    currDate = new Date(currDate.getFullYear(), currDate.getMonth()+1, 1);
    setUpCalendar();
}

function prevMonth() {
    currDate = new Date(currDate.getFullYear(), currDate.getMonth()-1, 1);
    setUpCalendar();
}

function displayDate(dateDivInst){
    if(dateDivInst.innerHTML===""){
        return;
    }
    let day =  {year:currDate.getFullYear(), month:currDate.getMonth()+1, day:dateDivInst.innerHTML};
    console.log(day);
}

function logIn(){
    //checks for valid login

    console.log("LogIn Clicked");
    document.getElementById("LogInForm").style.display = "none";
    document.getElementById("AddEventForm").style.display = "block";
}
function addEvent(){
    let dateToAdd = new Date(document.getElementById('EventDatetime').value);
    console.log(dateToAdd);
}

document.getElementById("prevMonthButton").addEventListener('click', prevMonth, false);
document.getElementById("nextMonthButton").addEventListener('click', nextMonth, false);
document.addEventListener("DOMContentLoaded", setUpCalendar, false);
document.getElementById("AddEventForm").style.display = "none";
logInButton.addEventListener("click", logIn, false);
logInButton.addEventListener("click", (e)=>{e.preventDefault(); logIn();}, false);
document.getElementById('addEventButton').addEventListener("click", (e) =>{e.preventDefault(); addEvent();}, false);

for(let i = 0; i<dayDivs.length; i++){
    dayDivs[i].addEventListener('click', function(){displayDate(this)}, false);
}