(function() {
  // Detect Safari (Desktop and Mobile) but exclude Chrome and Android
  var isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
  if (isSafari) {
    document.documentElement.classList.add('is-safari');
    
    // On Homepage and Search results, force horizontal card layout for Safari
    document.addEventListener('DOMContentLoaded', function() {
      if (document.body.classList.contains('home') || document.body.classList.contains('search')) {
        var cards = document.querySelectorAll('.post-card');
        cards.forEach(function(card) {
          if (card.classList.contains('post-card-horizontal')) return;
          card.classList.add('post-card-horizontal');
          
          var link = card.querySelector('.post-card-link');
          if (!link) return;
          
          var image = link.querySelector('.post-card-image');
          var header = link.querySelector('.post-card-header');
          var footer = link.querySelector('.post-card-footer');
          
          // Create wrapper for content if it doesn't exist
          var contentWrapper = link.querySelector('.post-card-content');
          if (!contentWrapper) {
            contentWrapper = document.createElement('div');
            contentWrapper.classList.add('post-card-content');
            
            // Move header and footer into wrapper
            if (header) contentWrapper.appendChild(header);
            if (footer) contentWrapper.appendChild(footer);
            
            // Append wrapper to link
            link.appendChild(contentWrapper);
          }
          
          // Ensure image is first child of link
          if (image) {
            link.insertBefore(image, link.firstChild);
          }
        });
      }
    });
  }
})();