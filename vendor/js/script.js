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
  grid.masonry({
    itemSelector: ".grid-item",
    columnWidth: 200,
    gutter: 5,
    isInitLayout: false,
  });
});

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
