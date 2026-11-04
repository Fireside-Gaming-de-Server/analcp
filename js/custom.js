$(document).ready(function(){
    $(".nav-form").submit(function(e) {
        e.preventDefault();
        let search = $('#TextSearchID').val();
        let searchTrim = search.trim();
        
        let groups;
        groups = searchTrim.match(/^v\s(.*)/);
        uid = searchTrim.match(/^u\s(.*)/);
        if (groups)  {
          document.location.href = "/vehicleShow.php?id=" + encodeURIComponent( groups[1] );
          return;
        };

        groups = searchTrim.match(/^g\s(.*)/);
        if (groups)  {
            document.location.href = "/datenbankGangs.php?search=" + encodeURIComponent( groups[1] );
          return;
        };

        groups = searchTrim.match(/^u\s(.*)/);
        if (groups)  {
            document.location.href = "/playerShow.php?id=" + encodeURIComponent( uid[1] );
          return;
        };
  
        if (searchTrim.substring(0, 3) == "765" && searchTrim.length == 17) {
          document.location.href = "/playerShow.php?id=" + encodeURIComponent( searchTrim );
          return false
        };

  
        document.location.href = "/datenbankSpielerLevelId.php?search=" + encodeURIComponent( search );      
    });
  });
// Highlight the current page using the links already permitted in the sidebar.
$(function () {
    var currentPath = window.location.pathname.replace(/\/$/, '').replace(/\.php$/, '');
    $('#menu a[href]').each(function () {
        var href = this.getAttribute('href');
        if (!href || href.charAt(0) === '#') return;
        var target = new URL(href, window.location.href);
        var targetPath = target.pathname.replace(/\/$/, '').replace(/\.php$/, '');
        if (target.origin === window.location.origin && targetPath === currentPath) {
            $(this).attr('aria-current', 'page').closest('li').addClass('nav-active');
            $(this).parents('li.nav-parent').addClass('nav-expanded nav-active');
        }
    });
});
