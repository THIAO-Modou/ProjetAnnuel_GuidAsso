if (typeof jQuery == 'undefined') {
    console.log(" jQuery N'EST PAS chargé !");
} else {
    console.log(" jQuery EST chargé !");
}

console.log(" Le fichier assoc_prediction.js est bien chargé !");

$(document).ready(function(){


    // Departement filter sent to the API (default from DB, override when checkbox is checked)
    function getDepartementFilter() {
        const checkbox = document.getElementById("horsDepartement");
        const input = document.getElementById("numeroDepartement");
        const error = document.getElementById("departementError");

        if (!input) return "";

        if (checkbox && checkbox.checked) {
            const value = (input.value || "").toString().trim();
            if (error) {
                error.style.display = value ? "none" : "block";
            }
            return value;
        }

        if (error) error.style.display = "none";
        return (input.dataset.default || input.value || "").toString().trim();
    }

    function setupAutocomplete(inputSelector, suggestionsBoxSelector, ajaxURL) {

        $(document).on('keyup', inputSelector, function(){
            const query = $(this).val().trim();

            if(query !== ''){
                const dep = getDepartementFilter();
                const checkbox = document.getElementById("horsDepartement");
                if (checkbox && checkbox.checked && dep === "") {
                    $(suggestionsBoxSelector).fadeOut();
                    return;
                }
                $.ajax({
                    url: ajaxURL,
                    method: "POST",
                    data: {query: query, dep: dep},
                    success: function(data){
                        $(suggestionsBoxSelector).fadeIn().html(data);
                    }
                });
            } else {
                $(suggestionsBoxSelector).fadeOut();
            }
        });

        $(document).on('click', suggestionsBoxSelector + ' li', function(){

            const data = $(this).data(); // récupère toutes les data-*

            // Remplit le champ concerné (association OU commune selon le champ)
            $(inputSelector).val(data.nom);

            $(suggestionsBoxSelector).fadeOut();

            // Émet l’événement correct
            $(document).trigger("associationSelected", data);
        });


        $(document).on('mouseover', suggestionsBoxSelector + ' li', function(){
            $(this).css('background-color', '#abcdf0');
        });

        $(document).on('mouseout', suggestionsBoxSelector + ' li', function(){
            $(this).css('background-color', '');
        });

        $(document).on('focusout', inputSelector, function(){
            setTimeout(() => $(suggestionsBoxSelector).fadeOut(), 200);
        });
    }
    // Appliquer l'autocomplétion aux champs d'association
    //setupAutocomplete('#association', '#associationSuggestions', '/../controllers/predict_PHP/recup_assoc.php'); // Nom association

    // Autocomplétion association
    setupAutocomplete('#association', '#associationSuggestions', '/../controllers/predict_PHP/api_association.php');

    setupAutocomplete('#association_fiche', '#associationSuggestions_fiche', '/../controllers/predict_PHP/recup_assoc.php'); // Fiche Asso 
});
