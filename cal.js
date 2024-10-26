const dayDivs = document.getElementsByClassName("day");
const monthLabel = document.getElementById("monthLabel");
const logInButton = document.getElementById("logInButton");
const dayOfEventsH1 = document.getElementById("dayOfEvent");
const eventSelected = document.getElementById("eventSelected");
let eventDivs = [];
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
    document.getElementById("dayListing").style.display = "block";
    dayOfEventsH1.innerHTML = mString(currDate) + ", " + day.day + " " + day.year;
    //get all the events and add a <li> and a <div> and <input> and <p>
    eventDivs = document.getElementsByClassName('eventListed')
    for(let i = 0; i<eventDivs.length; i++){
        eventDivs[i].addEventListener('click', highlightEvent, false);
    }
}

function logIn(){
    //checks for valid login

    //console.log("LogIn Clicked");
    document.getElementById("LogInForm").style.display = "none";
    document.getElementById("AddEventForm").style.display = "block";
}
function addEvent(){
    let dateToAdd = new Date(document.getElementById('EventDatetime').value);
    console.log(dateToAdd);
}

function highlightEvent(){
    eventSelected.style.display = "block";
    eventSelected.children[0].value = this.children[0].value;
    eventSelected.children[1].value = this.children[1].value;
    eventSelected.children[2].value = this.children[2].value;
    eventSelected.children[5].value = eventSelected.children[1].value;
    console.log(this)
    console.log(eventSelected)
    document.getElementById("nameOfEvent").innerHTML = this.children[2].value + ": " + this.children[1].value;
}

function editEvent(){
    //if(eventSelected.children)
    console.log(eventSelected.children[0].value)
}

function shareEvent(){
    console.log(this.parentElement.children[0].value)
}

document.getElementById("prevMonthButton").addEventListener('click', prevMonth, false);
document.getElementById("nextMonthButton").addEventListener('click', nextMonth, false);
document.addEventListener("DOMContentLoaded", setUpCalendar, false);
document.getElementById("AddEventForm").style.display = "none";
document.getElementById("eventSelected").style.display = "none";
document.getElementById("dayListing").style.display = "none";
document.getElementById("eventEditButton").addEventListener('click', editEvent, false);
document.getElementById("eventShareButton").addEventListener('click', shareEvent, false);
logInButton.addEventListener("click", logIn, false);
logInButton.addEventListener("click", (e)=>{e.preventDefault(); logIn();}, false);
document.getElementById('addEventButton').addEventListener("click", (e) =>{e.preventDefault(); addEvent();}, false);


for(let i = 0; i<dayDivs.length; i++){
    dayDivs[i].addEventListener('click', function(){displayDate(this)}, false);
}