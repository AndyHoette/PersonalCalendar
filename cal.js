const dayDivs = document.getElementsByClassName("day");
const monthLabel = document.getElementById("monthLabel");
const logInButton = document.getElementById("logInButton");
const createAccountButton = document.getElementById("createAccountButton");
const dayOfEventsH1 = document.getElementById("dayOfEvent");
const eventSelected = document.getElementById("eventSelected");
const listOfEvents = document.getElementById("listOfEvents");
const dot = document.getElementsByClassName("divWithDot")[0];
let eventDivs = [];
let currDate = new Date();

function daysInMonth(month, year){
    let newDate = new Date(year, month+1, 0);
    return newDate.getDate();
}

function setUpCalendar() {
    monthLabel.innerHTML = mString(currDate) + " " + currDate.getFullYear();
    let firstDayOfMonth = new Date(currDate.getFullYear(), currDate.getMonth(), 1).getDay();
    let maxDays = daysInMonth(currDate.getMonth(), currDate.getFullYear());
    //console.log(firstDayOfMonth+maxDays);
    for (let i = 0; i < dayDivs.length; i++) {
        dayDivs[i].innerHTML = "";
        dayDivs[i].appendChild(document.createElement("p"));
        if (i < firstDayOfMonth) {
            dayDivs[i].firstElementChild.innerHTML = "";
        } else if (i < firstDayOfMonth + maxDays) {
            dayDivs[i].firstElementChild.innerHTML = i - firstDayOfMonth + 1;
        } else {
            dayDivs[i].firstElementChild.innerHTML = "";
        }
    }
    //get a list of events for this month (between currDate.getMonth(),1 and currDate.getMonth(),maxDays
    listOfEventsThisMonth = [{day:1},{day:5}]
    for(let i = 0; i<listOfEventsThisMonth.length; i++){
        let dayOfEvent = listOfEventsThisMonth[i].day;
        let clonedDot = dot.cloneNode(true);
        clonedDot.style.display = "block";
        dayDivs[dayOfEvent-1+firstDayOfMonth].appendChild(clonedDot);
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
    if(dateDivInst.firstElementChild.innerHTML===""){
        return;
    }
    let day =  {year:currDate.getFullYear(), month:currDate.getMonth()+1, day:dateDivInst.firstElementChild.innerHTML};
    document.getElementById("dayListing").style.display = "block";
    dayOfEventsH1.innerHTML = mString(currDate) + ", " + day.day + " " + day.year;
    //get all the events and add a <li> and a <div> and <input> and <p>
    let events = [{id: 330, title:"This is obj title", when:new Date()}]
    listOfEvents.innerHTML = "";
    for(let i = 0; i<events.length; i++){
        let listItem = document.createElement("li");
        let itemDiv = document.createElement("div");
        itemDiv.className = "eventListed";
        listItem.appendChild(itemDiv);
        let hidden1 = document.createElement("input");
        hidden1.type = "hidden";
        hidden1.value = events[i].id;
        itemDiv.appendChild(hidden1);
        let hidden2 = document.createElement("input");
        hidden2.type = "hidden";
        hidden2.value = events[i].title;
        itemDiv.appendChild(hidden2);
        let hidden3 = document.createElement("input");
        hidden3.type = "hidden";
        hidden3.value = events[i].when;
        itemDiv.appendChild(hidden3);
        let itemP = document.createElement("p");
        itemP.innerHTML = hidden2.value;
        itemDiv.appendChild(itemP);
        listOfEvents.append(listItem);
        //console.log(listItem);
    }
    eventDivs = document.getElementsByClassName('eventListed')
    for(let i = 0; i<eventDivs.length; i++){
        eventDivs[i].addEventListener('click', highlightEvent, false);
    }
}

function logIn(){
    //checks for valid login
    console.log(this.parentElement);
    //console.log("LogIn Clicked");
    document.getElementById("LogInForm").style.display = "none";
    document.getElementById("createAccountForm").style.display = "none";
    document.getElementById("userGreeting").style.display = "block";
    document.getElementById("AddEventForm").style.display = "block";
}

function createAccount(b){
    console.log(b);
    console.log(b.parentElement);
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
    eventSelected.children[8].value = eventSelected.children[2].value;
    document.getElementById("nameOfEvent").innerHTML = this.children[2].value + ": " + this.children[1].value;
}

function editEvent(){
    //if(eventSelected.children)
    console.log(eventSelected.children[0].value);
    console.log(eventSelected.children[5].value);
    console.log(eventSelected.children[8].value);
}

function shareEvent(){
    console.log(this.parentElement.children[0].value); //event ID
    console.log(this.parentElement.children[1].value); //event Title
    console.log(this.parentElement.children[2].value); //event datetime
    console.log(this.parentElement.children[11].value);
}

function deleteEvent(){
    console.log(this.parentElement.children[0].value); //event ID
}

document.getElementById("AddEventForm").style.display = "none";
document.getElementById("userGreeting").style.display = "none";
document.getElementById("eventSelected").style.display = "none";
document.getElementById("dayListing").style.display = "none";
dot.style.display = "none";

document.addEventListener("DOMContentLoaded", setUpCalendar, false);

document.getElementById("prevMonthButton").addEventListener('click', prevMonth, false);
document.getElementById("nextMonthButton").addEventListener('click', nextMonth, false);
document.getElementById("eventEditButton").addEventListener('click', editEvent, false);
document.getElementById("eventShareButton").addEventListener('click', shareEvent, false);
document.getElementById("eventDeleteButton").addEventListener('click', deleteEvent, false);
logInButton.addEventListener("click", logIn, false);
logInButton.addEventListener("click", (e)=>{e.preventDefault(); logIn();}, false);
document.getElementById('addEventButton').addEventListener("click", (e) =>{e.preventDefault(); addEvent();}, false);
createAccountButton.addEventListener("click", (e) =>{e.preventDefault(); createAccount(createAccountButton);}, false);
createAccountButton.addEventListener("click", (e) =>{e.preventDefault(); createAccount(createAccountButton);}, false);


for(let i = 0; i<dayDivs.length; i++){
    dayDivs[i].addEventListener('click', function(){displayDate(this)}, false);
}