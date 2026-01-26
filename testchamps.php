<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire dynamique</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Style pour la boîte de suggestions */
        .suggestions-box {
            position: absolute;
            border: 1px solid #ccc;
            background-color: #fff;
            z-index: 1000;
            width: 300px;
        }
        .suggestions-box ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .suggestions-box li {
            padding: 8px;
            cursor: pointer;
        }
        .suggestions-box li:hover {
            background-color: #f0f0f0;
        }
    </style>
    <script>
        $(document).ready(function () {
            // Détecter les saisies dans le champ e-mail
            $('#email-input').keyup(function () {
                var query = $(this).val();
                if (query !== '') {
                    $.ajax({
                        url: 'autocomplete.php', // Appel vers le fichier PHP
                        method: 'POST',
                        data: { query: query },
                        success: function (data) {
                            $('#emailSuggestions').fadeIn(); // Afficher les suggestions
                            $('#emailSuggestions').html(data);
                        },
                    });
                } else {
                    $('#emailSuggestions').fadeOut(); // Masquer les suggestions si le champ est vide
                }
            });

            // Lorsqu'une suggestion est cliquée
            $(document).on('click', '#emailSuggestions li', function () {
                var selectedEmail = $(this).text();
                $('#email-input').val(selectedEmail); // Insérer la suggestion dans le champ d'entrée
                $('#emailSuggestions').fadeOut(); // Masquer la boîte de suggestions
            
                // Requête AJAX pour récupérer les détails de l'utilisateur
    $.ajax({
        url: 'get-details.php',
        method: 'POST',
        data: { email: selectedEmail },
        success: function (response) {
            var data = JSON.parse(response);
            if (data) {
                $('#nom').val(data.NOMPERSONNE || ''); // Remplir le champ "nom"
                $('#prenom').val(data.PRENOMPERSONNE || ''); // Remplir le champ "prénom"
                $('#classification').val(data.CLASSIFICATION || ''); // Remplir le champ "classification"
            } else {
                // Si aucune donnée n'est trouvée
                alert('Aucune information trouvée pour cet e-mail.');
            }
        },
        error: function () {
            alert('Une erreur est survenue lors de la récupération des détails.');
        }
    });
            });

            // Masquer la boîte de suggestions après un délai si on quitte le champ
            $('#email-input').focusout(function () {
                setTimeout(function () {
                    $('#emailSuggestions').fadeOut();
                }, 200);
            });

            // Réafficher les suggestions si elles existent lors du focus
            $('#email-input').focus(function () {
                if ($('#emailSuggestions').children().length > 0) {
                    $('#emailSuggestions').fadeIn();
                }
            });
        });
    </script>
</head>
<body>

<h1>Formulaire avec suggestions d'e-mail</h1>
<form action="update-details.php" method="POST">
    <div class="form-group">
        <label for="email-input">E-mail :</label>
        <input type="text" id="email-input" name="email" placeholder="Tapez l'e-mail" autocomplete="off" required>
    </div>
    
    <!-- Boîte de suggestions pour l'e-mail -->
    <div class="suggestions-box" id="emailSuggestions"></div><br>

    <!-- Champs pour afficher les détails -->
    <label for="nom">Nom :</label>
    <input type="text" id="nom" name="nom"><br><br>

    <label for="prenom">Prénom :</label>
    <input type="text" id="prenom" name="prenom"><br><br>

    <label for="classification">Classification :</label>
    <input type="text" id="classification" name="classification"><br><br>

    <button type="submit">Mettre à jour</button>
</form>

</body>
</html>
