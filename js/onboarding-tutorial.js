// Onboarding Hopscotch script for Spiral
// For documentation, see linkedin.github.io/hopscotch

// 1.
// Set cookie to prevent repeated tour launch

function setCookie(key, value) {
var expires = new Date();
expires.setTime(expires.getTime() + (1 * 24 * 60 * 60 * 1000));
document.cookie = key + '=' + value + ';path=/' + ';expires=' + expires.toUTCString();
};

function getCookie(key) {
var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
return keyValue ? keyValue[2] : null;
};


// 2.
// Define tour steps

var tour = {
          onEnd: function() {
            setCookie("toured", "toured");
            },
          onClose: function() {
            setCookie("toured", "toured");
            },
          id: "hello-hopscotch",
          steps: [

		// Steps on Dashboard page

            {
              title: "Welcomee to Spiral!",
              content: "Let's quickly go through the basics!<br>(you can always restart this tour from the footer)",
              target: 'div.booked-user-avatar',
              placement: "bottom"
            },

	    {
              title: "This is your Dashboard",
              content: "You will see new and upcoming bookings here.",
              target: 'li.active',
              placement: "bottom"
            },

            {
              title: "Customize your profile!",
              content: "Let's set up your profile page to give clients a good impression!",
              target: 'li.menu-item-130',
              placement: "bottom",
              nextOnTargetClick: true,
              multipage: true,
              onNext: function() {
                window.location = php_vars.url
              }
            },

		// Steps on Profile page

           {
              title: "Avatar and cover image",
              content: "Make sure you pick visuals that build trust",
              target: "div.pm-profile-image",
              arrowOffset: 30,
              yOffset: -60,
              placement: "bottom"
            },

            {
              title: "Introduction and video",
              content: "Everything you want potential clients to see, including your intro video!",
              target: "div.pm-edit-user",
              placement: "top"
            },

            {
              title: "Set up your calendar",
              content:"Once your calendar is created, you'll be taken to the Timeslots setup page.",
              target: "button.btn.btn-default",
              placement: "top",
              arrowOffset: 260,
              xOffset: -145,
              nextOnTargetClick: true,
              multipage: true,
              onNext: function() {
                window.location = "/add-calendar"
              }
            },

		// Steps on Timeslots page

           {
              title: "Weekly recurring timeslots",
              content: "Set your weekly timeslots here",
              target: "li.active",
              placement: "bottom",
              nextOnTargetClick: true
            },

            {
              title: "Holidays and others",
              content: "Disable specific days, or set one-off special booking times",
              target: "ul.booked-admin-tabs>li:nth-of-type(2)",
              placement: "bottom",
              nextOnTargetClick: true
            },

            {
              title: "Client information",
              content: "If you want to ask special questions during the booking process, set it up here.",
              target: "ul.booked-admin-tabs>li:nth-of-type(3)",
              placement: "bottom",
              nextOnTargetClick: true
            },

            {
              title: "That's it!",
              content: "You can get back to your Dashboard and Profile here.",
              target: "menu-Dashboard",
              placement: "bottom"
            }

          ]
        };





// 2.
// Start tour if it's the user's first time

if (!getCookie("toured")) {
hopscotch.startTour(tour);
}
