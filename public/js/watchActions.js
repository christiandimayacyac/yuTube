let toggleSubscribe = (btnClicked, videoId, encUserId) => {
    $.post("http://localhost/yutube/pages/ajaxToggleSubscribe", {btnClicked:btnClicked.className, videoId:videoId, encUserId: encUserId}) 
    .done((data)=>{
        let res = JSON.parse(data);
        console.log(res);
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
        if (data["flag"]!= "") {
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
        if (data["flag"]!= "") {
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
    });
}
