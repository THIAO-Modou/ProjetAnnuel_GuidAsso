if (typeof jQuery == 'undefined') {
    console.log("❌ jQuery N'EST PAS chargé !");
} else {
    console.log("✅ jQuery EST chargé !");
}

console.log("✅ Le fichier prenom_prediction.js est bien chargé !");

$(document).ready(function(){
    function setupPrenomAutocomplete(inputSelector, suggestionsBoxSelector, ajaxURL) {
        $(document).on('keyup', inputSelector, function(){
            var query = $(this).val();
            if(query !== ''){
                $.ajax({
                    url: ajaxURL,
                    method: "POST",
                    data: {query: query},
                    success: function(data){
                        $(suggestionsBoxSelector).fadeIn().html(data);
                    }
                });
            } else {
                $(suggestionsBoxSelector).fadeOut();
            }
        });

        // Lorsqu'on clique sur une suggestion
        $(document).on('click', suggestionsBoxSelector + ' li', function(){
            var selectedPrenom = $(this).text();
            $(inputSelector).val(selectedPrenom);
            $(suggestionsBoxSelector).fadeOut();
        });

        // Effets visuels pour le survol des suggestions
        $(document).on('mouseover', suggestionsBoxSelector + ' li', function(){
            $(this).css('background-color', '#abcdf0');
        });

        $(document).on('mouseout', suggestionsBoxSelector + ' li', function(){
            $(this).css('background-color', '');
        });

        // Masquer les suggestions quand on quitte le champ
        $(document).on('focusout', inputSelector, function(){
            setTimeout(function(){ $(suggestionsBoxSelector).fadeOut(); }, 200);
        });
    }

    // Appliquer l'autocomplétion aux champs de prénom de contact
   
    setupPrenomAutocomplete('#PcontactInput3', '#PcontactSuggestions3', '/../controllers/predict_PHP/recup_prenom_contact.php'); // Longsuivi
    
});