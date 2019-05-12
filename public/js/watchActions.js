//helper functions

//Makes the likes and dislikes numerical value show or hide depending on the value after the user clicks like or dislike button
let checkCounter = () => {
    
    const likeCount = document.getElementsByClassName("likeCount");
    const dislikeCount = document.getElementsByClassName("dislikeCount");

    if (likeCount[0].innerHTML.toString() === '0') {
        likeCount[0].innerHTML = "";
    }

    if (dislikeCount[0].innerHTML.toString() === '0') {
        dislikeCount[0].innerHTML = "";
    }

}

let toggleSubscribe = (btnClicked, videoId, encUserId, encUploaderId) => {
    //Get the button text for the subscribe/unsubscribe button
    let btnText = $(btnClicked).children().text();
    let btnTextArray = btnText.split(" ");

    //Confirm unsubscription if currently subscribed
    if ( btnText.includes("SUBSCRIBED") ) {
        let response = confirm("Are you sure you want to unsubscribe?");

        if (!response) {
            return;
        }
    }
    $.post("http://localhost/yutube/pages/ajaxToggleSubscribe", {btnClicked:btnClicked.className, videoId:videoId, encUserId: encUserId, encUploaderId: encUploaderId}) 
    .done((data)=>{
        let res = JSON.parse(data);
        // let res = (data);
        //Get the 2nd array that holds the subs number
        let currentSubs = (!btnTextArray[1]) ? "" : +btnTextArray[1];

        if ( btnText.includes("SUBSCRIBED") ) {
            if (currentSubs !== "") {
                currentSubs = +currentSubs - 1;
                //Set currentSubs to blank if value = 0
                if (currentSubs === 0) {
                    currentSubs = "";
                }
                else {
                    currentSubs = " " + currentSubs;
                }
            }
 
            $(btnClicked).children().text("SUBSCRIBE" + currentSubs);
        }
        else if ( btnText.includes("EDIT") ) {
            console.log("Edit the video...");
        }
        else {
            if (currentSubs !== "") {
                currentSubs = " " + (+currentSubs + 1);
            }
            else {
                currentSubs = " 1";
            }
            
            $(btnClicked).children().text("SUBSCRIBED" + currentSubs);


        }
        
    });
}

let likeVideo = (videoId, encUID) => {
    $.post("http://localhost/yutube/pages/ajaxLikeVideo", {videoId: videoId, encUID: encUID})
    .done((data)=>{
        // console.log(JSON.parse(data));
        data = (JSON.parse(data));

        //Select the buttons from the DOM
        let likeButton = $(".video-primary-info button").first();
        let dislikeButton = $(".video-primary-info button").last();
        

        //Add or remove active class depending on the returned flag value
        if (data["flag"]!= "") { //flag => contains either "like" or "dislike value" that determines the operation to be done
            // Set active class in the like button
            $(likeButton).addClass("active");
            $(dislikeButton).removeClass("active");

            // Change the thumb-icons
            $(likeButton).find("img").attr("src", "../../../images/icons/thumb-up-active.png");
            $(dislikeButton).find("img").attr("src", "../../../images/icons/thumb-down.png");
        }
        else {
            // Remove active class in the like button
            $(likeButton).removeClass("active");

            // Change the thumb-icons
            $(likeButton).find("img").attr("src", "../../../images/icons/thumb-up.png");
        }

        // Change the numeric value for likes
        $(".video-primary-info button").siblings("span:first").text(data['likes']);
        $(".video-primary-info button").siblings("span:last").text(data['dislikes']);

        //Check if the number of likes is equal to zero then hide it if true
        checkCounter();
    });
}

let dislikeVideo = (videoId, encUID) => {
    $.post("http://localhost/yutube/pages/ajaxDislikeVideo", {videoId: videoId, encUID: encUID})
    .done((data)=>{
        data = (JSON.parse(data));

        //Select the buttons from the DOM
        let likeButton = $(".video-primary-info button").first();
        let dislikeButton = $(".video-primary-info button").last();

        //Add or remove active class depending on the returned flag value
        if (data["flag"]!= "") { //flag => contains either "like" or "dislike value" that determines the operation to be done
            // Set active class in the dislike button
            $(dislikeButton).addClass("active");
            $(likeButton).removeClass("active");
            
            // Change the thumb-icons
            $(dislikeButton).find("img").attr("src", "../../../images/icons/thumb-down-active.png");
            $(likeButton).find("img").attr("src", "../../../images/icons/thumb-up.png");
        }
        else {
            // Remove active class in the dislike button
            $(dislikeButton).removeClass("active");

            // Change the thumb-icons
            $(dislikeButton).find("img").attr("src", "../../../images/icons/thumb-down.png");
        }

        // Change the numeric value for likes
        $(".video-primary-info button").siblings("span:first").text(data['likes']);
        $(".video-primary-info button").siblings("span:last").text(data['dislikes']);

        //Check if the number of likes is equal to zero then hide it if true
        checkCounter();
    });
}
