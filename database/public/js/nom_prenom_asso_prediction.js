if (typeof jQuery === 'undefined') {
    console.log(" jQuery N'EST PAS chargé !");
} else {
    console.log(" jQuery EST chargé !");
}

$(document).ready(function () {
    function setupNomAutocomplete(inputSelector, suggestionsBoxSelector, ajaxURL) {
        $(document).on('keyup', inputSelector, function () {
            const query = $(this).val().trim();
            if (query !== '') {
                $.ajax({
                    url: ajaxURL,
                    method: 'POST',
                    data: { query: query },
                    success: function (data) {
                        $(suggestionsBoxSelector).fadeIn().html(data);
                    }
                });
            } else {
                $(suggestionsBoxSelector).fadeOut();
            }
        });

        // Lorsqu'on clique sur une suggestion
        $(document).on('click', suggestionsBoxSelector + ' li', function () {
            const nomContact = $(this).attr('data-nomcontact'); // 🔥 Récupère l'attribut uniquement
            console.log(" Nom inséré :", nomContact);
            $(inputSelector).val(nomContact);
            $(suggestionsBoxSelector).fadeOut();
        });

        // Apparence au survol
        $(document).on('mouseover', suggestionsBoxSelector + ' li', function () {
            $(this).css('background-color', '#abcdf0');
        });

        $(document).on('mouseout', suggestionsBoxSelector + ' li', function () {
            $(this).css('background-color', '');
        });

        // Masque les suggestions quand on quitte le champ
        $(document).on('focusout', inputSelector, function () {
            setTimeout(() => $(suggestionsBoxSelector).fadeOut(), 200);
        });
    }

    // Initialisation
    setupNomAutocomplete('#NcontactInput_fiche', '#contactSuggestions_fiche', '/../controllers/predict_PHP/recup_nom_prenom_asso.php');
});
