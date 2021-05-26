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

  let supported_languages = [ 'cs', 'da', 'de', 'el', 'en', 'es', 'fi', 'fr', 'it', 'ja', 'nl', 'pl', 'pt', 'ru', 'sv', 'tr'];

  let textOptOut = {
    'cs': "Aktuálně jste vyloučeni. Chcete-li se přihlásit, zaškrtněte toto políčko.",
    'da': "Du har fravalgt tracking. Afkryds feltet for at tillade tracking.",
    'de': "Ihr Besuch dieser Webseite wird aktuell von der Matomo Webanalyse nicht erfasst. Diese Checkbox aktivieren für Opt-In.",
    'el': "Η επίσκεψή σας δεν καταγράφεται. Επιλέξτε το πλαίσιο επιλογής για opt-in.",
    'en': "You are currently opted out. Click here to opt in.",
    'es': "Actualmente está siendo excluido. Marque esta casilla para adherirse.",
    'fi': "Et ole mukana seurannassa. Lisää valinta osallistuaksesi seurantaan.",
    'fr': "Vous n'êtes actuellement pas suivi(e). Cochez cette case pour ne plus être exclu(e).",
    'it': "Al momento non hai accettato il programma. Metti la spunta a questa casella per abilitarti (opt-in).",
    'ja': "現在オプトアウトされています。 オプトインするには、このチェックボックスをオンにします。",
    'nl': "U bent momenteel afgemeld. Schakel dit vakje in om u aan te melden.",
    'pl': "Wykluczono Cię z procesu analityki statystycznej Zaznacz to pole aby włączyć analizę.",
    'pt': "Atualmente está excluído. Marque esta caixa para participar.",
    'ru': "Вы отказались от сбора статистики. Установите этот флажок, чтобы подписаться.",
    'sv': "Du är just nu exkluderad. Markera rutan för att vara med.",
    'tr': "Şu anda izlenmiyorsunuz. İzlemeyi etkinleştirmek için bu kutuyu işaretleyin."
  }

  let textOptIn = {
    'cs': "Nejste vyloučeni. Zrušte zaškrtnutí tohoto políčka pro odhlášení.",
    'da': "Du tillader tracking. Fjern markeringen for at fravælge tracking.",
    'de': "Ihr Besuch dieser Webseite wird aktuell von der Matomo Webanalyse erfasst. Diese Checkbox abwählen für Opt-Out.",
    'el': "Δεν έχετε επιλέξει να μην καταγράφεστε. Αποεπιλέξτε το πλαίσιο επιλογής για opt-out.",
    'en': "You are currently opted in. Click here to opt out.",
    'es': "Está siendo rastreado Desmarque esta casilla para excluirse.",
    'fi': "Et ole kieltänyt seurantaa Poista valinta tästä estääksesi seurannan.",
    'fr': "Vous n'êtes pas exclu(e). Décochez cette case pour être exclu(e).",
    'it': "Al momento non sei escluso dal programma. Togli la spunta a questa casella per escluderti (opt-out).",
    'ja': "オプトアウトされていません。 オプトアウトするには、このチェックボックスをオフにします。",
    'nl': "U bent momenteel aangemeld. Schakel dit vakje uit om u af te melden.",
    'pl': "Nie zrezygnowałeś z udziału w procesie analityki statystycznej. Odznacz to pole aby wyłączyć analizę.",
    'pt': "Não deixou de participar. Desmarque esta caixa para cancelar participação.",
    'ru': "Вы не отказались от сбора статистики. Снимите этот флажок, чтобы отказаться.",
    'sv': "Du har inte valt bort det. Avmarkera rutan för att inte vara med.",
    'tr': "İzni iptal etmemişsiniz. İzlemeyi devre dışı bırakmak için bu kutudaki işareti kaldırın."
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
  if ( optOut !== null ) {
    optOut.addEventListener("click", function() {
      if (this.checked) {
        _paq.push(['forgetUserOptOut']);
      } else {
        _paq.push(['optUserOut']);
      }
      setOptOutText(optOut);
    });
    setOptOutText(optOut);
  }
});

