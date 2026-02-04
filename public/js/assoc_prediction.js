if (typeof jQuery == 'undefined') {
    console.log(" jQuery N'EST PAS chargé !");
} else {
    console.log(" jQuery EST chargé !");
}

console.log(" Le fichier assoc_prediction.js est bien chargé !");

$(document).ready(function(){
    function setupAutocomplete(inputSelector, suggestionsBoxSelector, ajaxURL) {
        $(document).on('keyup', inputSelector, function(){
            var query = $(this).val().trim();
            if(query !== ''){
                $.ajax({
                    url: ajaxURL,
                    method: "POST",
                    data: {query: query},
                    success: function(data){
                        console.log("Réponse brute :", data);
                        $(suggestionsBoxSelector).fadeIn().html(data);
                    }
                });
            } else {
                $(suggestionsBoxSelector).fadeOut();
            }
        });

        // Lorsqu'on clique sur une suggestion
        $(document).on('click', suggestionsBoxSelector + ' li', function(){
            var selectedValue = $(this).attr('data-nom'); // Récupère l'association
            var selectedCodePostal = $(this).attr('data-code'); // Récupère le code postal
            var inputField = $(inputSelector);

            // Insère uniquement le NOMASSO
            inputField.val(selectedValue);
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

    // Appliquer l'autocomplétion aux champs d'association
    //setupAutocomplete('#association', '#associationSuggestions', '/../controllers/predict_PHP/recup_assoc.php'); // Nom association
    setupAutocomplete(
        '#association',
        '#associationSuggestions',
        '/../controllers/predict_PHP/api_association.php'
    );

    setupAutocomplete('#association_fiche', '#associationSuggestions_fiche', '/../controllers/predict_PHP/recup_assoc.php'); // Fiche Asso 
});

