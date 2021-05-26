//
// Matomo Opt Out
// 
// This script adds a listener that waits for a click on an element with id #matomo-optout
// and then toggles the current state of consent to matomo user tracking (opt in / opt out)
// 
// You need to add something like this to your page:
// 
// <p>
//   <input type="checkbox" id="matomo-optout" />
//   <label for="matomo-optout"><strong></strong></label>
// </p>
// 
// This replaces the none-responsive iframe opt-out and is inspired by this article:
// https://developer.matomo.org/guides/tracking-javascript-guide#optional-creating-a-custom-opt-out-form
// 

document.addEventListener("DOMContentLoaded", function(event) {

  let supported_languages = [ 'en', 'de', 'fr' ];

  let textOptOut = {
    'en': "You are currently opted out. Click here to opt in.",
    'de': "Ihr Besuch dieser Webseite wird aktuell von der Matomo Webanalyse nicht erfasst. Diese Checkbox aktivieren für Opt-In.",
    'fr': "Vous n'êtes actuellement pas suivi(e). Cochez cette case pour ne plus être exclu(e)."
  }

  let textOptIn = {
    'en': "You are currently opted in. Click here to opt out.",
    'de': "Ihr Besuch dieser Webseite wird aktuell von der Matomo Webanalyse erfasst. Diese Checkbox abwählen für Opt-Out.",
    'fr': "Vous n'êtes pas exclu(e). Décochez cette case pour être exclu(e)."
  }

  let browser_language = navigator.language.substr(0, 2);
  let lang = ( supported_languages.includes(browser_language) ) ? browser_language : 'en';

  function setOptOutText(element) {
    _paq.push([function() {
      element.checked = !this.isUserOptedOut();
      document.querySelector('label[for=matomo-optout] strong').innerText = this.isUserOptedOut()
        ? textOptOut[lang] : textOptIn[lang]
    }]);
  }

  var optOut = document.getElementById("matomo-optout");
  optOut.addEventListener("click", function() {
    if (this.checked) {
      _paq.push(['forgetUserOptOut']);
    } else {
      _paq.push(['optUserOut']);
    }
    setOptOutText(optOut);
  });
  setOptOutText(optOut);
});

