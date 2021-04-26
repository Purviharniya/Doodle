var timer;

$(document).ready(function () {
  //   console.log("Hello");

  $(".result").on("click", function () {
    console.log("clicked");

    var url = $(this).attr("href");
    var linkid = $(this).attr("data-linkid");
    console.log(url);
    console.log(linkid);
    if (!linkid) {
      alert("data link id attribute not found");
    }
    increaseClicks(linkid, url);
    return false;
  });

  var grid = $(".imageResults");

  grid.on("layoutComplete", function () {
    $(".grid-item img").css("visibility", "visible");
  });

  grid.masonry({
    itemSelector: ".grid-item",
    columnWidth: 200,
    gutter: 5,
    isInitLayout: false,
  });
});

function loadImage(src, classname) {
  // console.log(src);
  var image = $("<img>");
  image.on("load", function () {
    $("." + classname + " a").append(image);

    clearTimeout(timer);

    timer = setTimeout(function () {
      $(".imageResults").masonry();
    }, 500);
  });

  image.on("error", function () {
    $("." + classname).remove();

    $.post("ajax/setBroken.php", { src: src }).done(function (res) {
      if (res != "") {
        alert(res);
        return;
      } else {
      }
    });
  });

  image.attr("src", src);
}

function increaseClicks(linkid, url) {
  $.post("ajax/increaseCount.php", { linkid: linkid }).done(function (res) {
    if (res != "") {
      alert(res);
      return;
    } else {
      window.location.href = url;
    }
  });
}
