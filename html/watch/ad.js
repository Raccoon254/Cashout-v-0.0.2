
const popup = document.getElementById("pop");
var displayStyle = window.getComputedStyle(popup).getPropertyValue("display");
const myVideo = document.getElementById("my-video");
let totalPlaytime = 0;
let currentSourceIndex = 0;

if (displayStyle === "block") {
  //alert("Cheeze");
} else if (displayStyle === "none") {
  //alert("Geeze");
}


// Check if #pop is visible before autoplaying
function adPlay() {

        const myVideo = document.getElementById("my-video");
        //const display = document.getElementById("display");
        const videoSources = [
            "./videos/c.mp4",
            "./videos/b.mp4",
            "./videos/d.mp4",
            "./videos/e.mp4"
        ];
    let totalPlaytime = 0;
    myVideo.play();
    
        myVideo.addEventListener("ended", function () {
            // Increment the current source index
            currentSourceIndex++;
    
            // Check if we've reached the end of the sources array
            if (currentSourceIndex >= videoSources.length) {
                // If we have, reset the index to 0 to loop back to the start
                currentSourceIndex = 0;
                totalPlaytime = 0;
            }
    
            // Add the current video's duration to the total playtime
            totalPlaytime += myVideo.duration;
    
            // Set the new video source
            myVideo.src = videoSources[currentSourceIndex];
    
            // Play the new video
            myVideo.play();
        });
    
        myVideo.addEventListener("timeupdate", function () {
            // Get the current time of the video
            const currentTime = myVideo.currentTime;
    
            // Update the display with the current time and total playtime
            //display.innerHTML = "Total Live Play Time: " + formatTime(totalPlaytime + currentTime);
    
            // Check if the total playtime is greater than or equal to 30 seconds
            if (totalPlaytime + currentTime >= 29) {
                // If it is, alert the user and pause the current video
                myVideo.pause();
                myVideo.currentTime = 0;
            }
        });
    
        function formatTime(time) {
            // Convert the time to minutes and seconds
            const minutes = Math.floor(time / 60);
            const seconds = Math.floor(time % 60);
    
            // Add leading zeros if necessary
            const formattedMinutes = minutes < 10 ? "0" + minutes : minutes;
            const formattedSeconds = seconds < 10 ? "0" + seconds : seconds;
    
            // Return the formatted time
            return formattedMinutes + ":" + formattedSeconds;
        }
}




var disableLinks = false;
var countdownInterval = null;
var remainingSeconds = 30;

function disableNavigation() {
    disableLinks = true;
    var links = document.getElementsByTagName("a");
    for (var i = 0; i < links.length; i++) {
        links[i].onclick = function (e) {
            e.preventDefault();
        };
    }
}

function enableNavigation() {
    disableLinks = false;
    var links = document.getElementsByTagName("a");
    for (var i = 0; i < links.length; i++) {
        links[i].onclick = null;
    }
}

function startCountdown() {
    adPlay();
    pausePlay();

    document.getElementById("countdown").innerHTML = remainingSeconds;
    countdownInterval = setInterval(function () {
        remainingSeconds--;
        document.getElementById("countdown").innerHTML = remainingSeconds;
        if (remainingSeconds <= 0) {
            clearInterval(countdownInterval);
            popup.style.display = "none";
            enableNavigation();
            document.getElementById("output").innerHTML = "Countdown successful!";
            currentSourceIndex = 0;
            myVideo.pause();
            myVideo.currentTime=0;
            remainingSeconds = 30;
        }
    }, 1000);
}

function pauseCountdown() {
    clearInterval(countdownInterval);
}

function showPopup() {
    currentSourceIndex = 0;
    if (popup.style.display === "block") {
        return;
    }
    disableNavigation();
    popup.style.display = "block";
    startCountdown();
    window.addEventListener("blur", pauseCountdown);
    window.addEventListener("focus", function () {
        if (remainingSeconds > 0) {
            startCountdown();
            console.log("Err");
        }
    });
}

function closePopup() {
    currentSourceIndex = 0;
    myVideo.pause();
    myVideo.currentTime=0;
    restartAnimation();
    totalPlaytime = 0;
    var popup = document.getElementById("pop");
    var currentTime = new Date().toLocaleTimeString();
    popup.style.display = "none";
    enableNavigation();
    clearInterval(countdownInterval);
    remainingSeconds = 30;
    window.removeEventListener("blur", pauseCountdown);
    window.removeEventListener("focus", startCountdown);
    document.getElementById("output").innerHTML = "Countdown cancelled at " + currentTime;
}




const numberContainer = document.getElementById("number-container");
const adProgress = document.getElementById("adProg");
let intervalId;
let isPlaying = false;
let remainingTime = remainingSeconds;

function startCounting() {
    let i = 1;
    intervalId = setInterval(() => {
        numberContainer.textContent = i;
        adProgress.style.width = i + "%";
        i++;
        if (i > 100) {
            clearInterval(intervalId);
        }
    }, 300);

    remainingTime = 30000 - (i - 1) * 300;
}

function pausePlay() {
    if (!isPlaying) {
        if (remainingTime > 0) {
            intervalId = setInterval(() => {
                numberContainer.textContent++;
                adProgress.style.width = (numberContainer.textContent - 1) + "%";
                remainingTime -= 300;
                if (numberContainer.textContent > 100) {
                    clearInterval(intervalId);
                }
            }, 300);
        } else {
            startCounting();
        }
    } else {
        clearInterval(intervalId);
        remainingTime = 30000 - (numberContainer.textContent - 1) * 300;
    }
    isPlaying = !isPlaying;
}

function restartAnimation() {
    clearInterval(intervalId);
    numberContainer.textContent = "";
    adProgress.style.width = "0";
    remainingTime = 0;
    isPlaying = false;
}



