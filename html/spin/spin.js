let innerWheel = new Winwheel({
    'numSegments': 8,
    'outerRadius': 150,        // Set the outer radius to make the wheel smaller than the outer wheel.
    'innerRadius': 110,
    'textAlignment': 'inner',
    'segments': [
        { 'fillStyle': '#FFB84C', 'text': '5' },//0
        { 'fillStyle': '#89f26e', 'text': '1' },//1
        { 'fillStyle': '#7de6ef', 'text': '4' },//0
        { 'fillStyle': '#FFB84C', 'text': '1' },//1
        { 'fillStyle': '#eae56f', 'text': '10' },//0
        { 'fillStyle': '#FFB84C', 'text': '1' },//1
        { 'fillStyle': '#eae56f', 'text': '2' },//0
        { 'fillStyle': '#7de6ef', 'text': '1' },//2
    ]

});
// Create the wheel
let theWheel = new Winwheel({
    'numSegments': 8,     // Specify number of segments.
    'textFontSize': 20,    // Set font size as desired.
    'outerRadius': 250,
    'textAlignment': 'inner',
    'innerRadius': 150,
    // This wheel is responsive!
    'segments':        // Define segments including color and text.
        [
            { 'fillStyle': '#1167b1', 'text': '  400' },
            { 'fillStyle': '#2a9df4', 'text': '  050' },
            { 'fillStyle': '#1167b1', 'text': '  000' },
            { 'fillStyle': '#2a9df4', 'text': '  100' },
            { 'fillStyle': '#1167b1', 'text': '  200' },
            { 'fillStyle': '#2a9df4', 'text': '  010' },
            { 'fillStyle': '#1167b1', 'text': '000' },
            { 'fillStyle': '#2a9df4', 'text': '500' }
        ],
    'pins':
    {
        'number': 16,
        'fillStyle': 'white',
        'outerRadius': 5,
        'responsive': true,  // This must be set to true if pin size is to be responsive, if not just location is.
    },
    'animation':           // Specify the animation to use.
    {
        'type': 'spinToStop',
        'duration': 5,     // Duration in seconds.
        'spins': -10,     // Number of complete spins.
        'callbackFinished': alertPrize,
        'easing': 'Power3.easeOut',
        'callbackAfter': drawInnerWheel,
        'callbackSound': playAudio,   // Function to call when the tick sound is to be triggered.
        'soundTrigger': 'pin'
    }
});

theWheel.draw();
innerWheel.draw(false);

function drawInnerWheel() {
    // Update the rotationAngle of the innerWheel to match that of the outer wheel - this is a big part of what
    // links them to appear as one 2-part wheel. Call the draw function passing false so the outer wheel is not wiped.
    innerWheel.rotationAngle = -(theWheel.rotationAngle);
    innerWheel.draw(false);
}

// Loads the tick audio sound in to an audio object.
let audio = new Audio('tick.mp3');

// This function is called when the sound is to be played.
async function playSound() {
    // Stop and rewind the sound if it already happens to be playing.
    audio.pause();
    audio.currentTime = 0;


    // Play the sound.
    if (audio.paused) {
        await audio.play();
    }
}
async function playAudio() {
    const audio = new Audio('tick.mp3');
    if (audio.paused) {
        await audio.play();
    }
}


let wheelPower = 2;
let wheelSpinning = false;

// -----------------------------------------------------------------
// Called by the onClick of the canvas, starts the spinning.
function startSpin() {
    if (wheelSpinning) {
        return;
    }
    theWheel.rotationAngle = theWheel.rotationAngle % 360;
    innerWheel.rotationAngle = innerWheel.rotationAngle % 360;
    // Ensure that spinning can't be clicked again while already running.
    if (wheelSpinning == false) {
        // Reset things with inner and outer wheel so spinning will work as expected. Without the reset the
        // wheel will probably just move a small amount since the rotationAngle would be close to the targetAngle
        // figured out by the animation.
        theWheel.stopAnimation(false);  // Stop the animation, false as param so does not call callback function.
        theWheel.rotationAngle = 0;     // Re-set the wheel angle to 0 degrees.
        theWheel.draw();                 // Call draw to render changes to the wheel.
        innerWheel.rotationAngle = 0;
        innerWheel.draw();

        // Based on the power level selected adjust the number of spins for the wheel, the more times is has
        // to rotate with the duration of the animation the quicker the wheel spins.
        if (wheelPower == 1) {
            theWheel.animation.spins = 3;     // Number of spins and/or duration can be altered to make the wheel
            theWheel.animation.duration = 7;  // appear to spin faster or slower.
        } else if (wheelPower == 2) {
            theWheel.animation.spins = 8;
            theWheel.animation.duration = 7;
        } else if (wheelPower == 3) {
            theWheel.animation.spins = 15;
        }

        // Disable the spin button so can't click again while wheel is spinning.

        // Begin the spin animation by calling startAnimation on the wheel object.
        theWheel.startAnimation();

        // Set to true so that power can't be changed and spin button re-enabled during
        // the current animation. The user will have to reset before spinning again.
        wheelSpinning = true;
    }
}
// -------------------------------------------------------
// Called when the spin animation has finished by the callback feature of the wheel because I specified callback in the parameters.
// -------------------------------------------------------
function alertPrize() {
    let winningInnerSegment = innerWheel.getIndicatedSegment();
    let winningOuterSegment = theWheel.getIndicatedSegment();
    let finalWinPrice = winningInnerSegment.text * winningOuterSegment.text;

    if (finalWinPrice==0) {
    } else {
        // Update user's balance in the database
        let xhr = new XMLHttpRequest();
        xhr.open('POST', 'update_balance.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) { 
                alert('You won ' + finalWinPrice + '. Your new balance is ' + this.responseText);
            }
        };
        xhr.send('amount=' + finalWinPrice);
    }
    
    theWheel.stopAnimation(false);  // Stop the animation, false as param so does not call callback function.
    theWheel.rotationAngle = 0;     // Re-set the wheel angle to 0 degrees.
    theWheel.draw();                // Call draw to render changes to the wheel.

    wheelSpinning = false; 

}


