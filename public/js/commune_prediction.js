if (typeof jQuery == 'undefined') {
    console.log(" jQuery N'EST PAS chargé !");
} else {
    console.log(" jQuery EST chargé !");
}

console.log(" Le fichier commune_prediction.js est bien chargé !"); // pour tester si ok dans la console


$(document).ready(function(){
    function setupCommuneAutocomplete(inputSelector, suggestionsBoxSelector, ajaxURL) {
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
            var selectedCommune = $(this).text(); // Récupère la valeur cliquée
            $(inputSelector).val(selectedCommune);
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

    // Appliquer l'autocomplétion uniquement aux communes
    setupCommuneAutocomplete('#commune', '#communeSuggestions', '/../controllers/predict_PHP/recup_com.php'); // Q&R
});
