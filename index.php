<?php
// CORRECTION : On force l'affichage de toutes les erreurs PHP pour le débogage.
// Cela nous permettra de voir la cause exacte de l'écran blanc.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$pdo = null;
$capsules = [];
$db_error = null;
$first_video_link = '';
$first_capsule_id = '';



try {
    include_once 'modules/db.php';

    // Sécurité supplémentaire : on vérifie que la connexion a bien été établie
    if (!isset($pdo) || $pdo === null) {
        throw new Exception("L'objet de connexion PDO n'a pas été initialisé. Vérifiez le fichier 'modules/db.php'.");
    }

    $stmt = $pdo->query("SELECT id, nom, lien_youtube FROM capsules ORDER BY nom ASC");
    $capsules = $stmt->fetchAll();

    if (!empty($capsules)) {
        $first_video_link = $capsules[0]['lien_youtube'];
        $first_capsule_id = $capsules[0]['id'];
    }
} catch (Throwable $e) { // Attrape les PDOException ET les autres erreurs
    // On stocke un message d'erreur clair pour l'afficher à l'utilisateur
    $db_error = "Erreur critique : Impossible de charger les données. (" . $e->getMessage() . ")";
    // Pour le débogage, il est utile de logger l'erreur complète
    error_log($e);
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <link rel="icon" type="image/png" href="img/_logo_UFOLEP_Gym_Trampo.jpg" />
    <title>Question-UFOLEP Gym</title>
</head>

<body>
    <nav>
        <div class="col">
            <img src="img/_logo_UFOLEP_Gym_Trampo.jpg" class="logo" width="100px" alt="Logo UFOLEP Gym">
            <h3>Nouveau Programme Technique</h3>
        </div>
        <div id="menu-toggle" class="menu-icon">
            <span></span>
            <span></span>
            <span></span>
        </div>

    </nav>
    <aside>
        <ul>
            <?php if (isset($db_error)): ?>
            <li style="color: red; padding: 10px; background-color: #ffebee; border: 1px solid #e57373;">
                <?= htmlspecialchars($db_error) ?></li>
            <?php elseif (!empty($capsules)): ?>
            <?php foreach ($capsules as $index => $capsule): ?>
            <?php
                    $embedLink = convertirLienYoutubeEnEmbed($capsule['lien_youtube']);
                    ?>
            <li data-youtube-link="<?= htmlspecialchars($embedLink) ?>"
                data-capsule-id="<?= htmlspecialchars($capsule['id']) ?>" class="<?= $index === 0 ? 'active' : '' ?>">
                <?= htmlspecialchars($capsule['nom']) ?>
            </li>
            <?php endforeach; ?>
            <?php else: ?>
            <li>Aucune vidéo trouvée.</li>
            <?php endif; ?>
        </ul>
    </aside>
    <main>
        <div class="container">
            <?php
            $first_video_embed_link = convertirLienYoutubeEnEmbed($first_video_link);
            ?>
            <iframe id="youtube-player" src="<?= htmlspecialchars($first_video_embed_link) ?>"
                title="YouTube video player" frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen></iframe>

            <form action="modules/send_question.php" method="POST" id="question-form">
                <br>
                <h3>Cette vidéo du nouveau programme technique UFOLEP a sans doute soulevé chez vous quelques
                    interrogations ? <br>
                    <br>
                    Ce formulaire est là pour ça !
                    <br>
                    <br>
                    Partagez vos questions, vos impressions ou les points que vous souhaitez voir approfondis.
                    <br>
                    <br>
                    L’équipe vous répondra prochainement !
                </h3>

                <input type="hidden" id="id_capsule_input" name="id_capsule"
                    value="<?= htmlspecialchars($first_capsule_id) ?>">

                <div class="form-row">
                    <div class="form-group">
                        <label>Niveau :</label>
                        <div class="radio-group">
                            <input type="radio" id="option1" name="choice" value="G1" checked>
                            <label for="option1">G1</label>
                            <input type="radio" id="option2" name="choice" value="G2">
                            <label for="option2">G2</label>
                            <input type="radio" id="option3" name="choice" value="G3">
                            <label for="option3">G3</label>
                            <input type="radio" id="option4" name="choice" value="G4">
                            <label for="option4">G4</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="depart">Votre département :</label>
                        <select name="depart" id="depart">
                            <option value="01">01</option>
                            <option value="02">02</option>
                            <option value="03">03</option>
                            <option value="04">04</option>
                            <option value="05">05</option>
                            <option value="06">06</option>
                            <option value="07">07</option>
                            <option value="08">08</option>
                            <option value="09">09</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                            <option value="13">13</option>
                            <option value="14">14</option>
                            <option value="15">15</option>
                            <option value="16">16</option>
                            <option value="17">17</option>
                            <option value="18">18</option>
                            <option value="19">19</option>
                            <option value="2A">2A</option>
                            <option value="2B">2B</option>
                            <option value="21">21</option>
                            <option value="22">22</option>
                            <option value="23">23</option>
                            <option value="24">24</option>
                            <option value="25">25</option>
                            <option value="26">26</option>
                            <option value="27">27</option>
                            <option value="28">28</option>
                            <option value="29">29</option>
                            <option value="30">30</option>
                            <option value="31">31</option>
                            <option value="32">32</option>
                            <option value="33">33</option>
                            <option value="34">34</option>
                            <option value="35">35</option>
                            <option value="36">36</option>
                            <option value="37">37</option>
                            <option value="38">38</option>
                            <option value="39">39</option>
                            <option value="40">40</option>
                            <option value="41">41</option>
                            <option value="42">42</option>
                            <option value="43">43</option>
                            <option value="44">44</option>
                            <option value="45">45</option>
                            <option value="46">46</option>
                            <option value="47">47</option>
                            <option value="48">48</option>
                            <option value="49">49</option>
                            <option value="50">50</option>
                            <option value="51">51</option>
                            <option value="52">52</option>
                            <option value="53">53</option>
                            <option value="54">54</option>
                            <option value="55">55</option>
                            <option value="56">56</option>
                            <option value="57">57</option>
                            <option value="58">58</option>
                            <option value="59">59</option>
                            <option value="60">60</option>
                            <option value="61">61</option>
                            <option value="62">62</option>
                            <option value="63">63</option>
                            <option value="64">64</option>
                            <option value="65">65</option>
                            <option value="66">66</option>
                            <option value="67">67</option>
                            <option value="68">68</option>
                            <option value="69">69</option>
                            <option value="70">70</option>
                            <option value="71">71</option>
                            <option value="72">72</option>
                            <option value="73">73</option>
                            <option value="74">74</option>
                            <option value="75">75</option>
                            <option value="76">76</option>
                            <option value="77">77</option>
                            <option value="78">78</option>
                            <option value="79">79</option>
                            <option value="80">80</option>
                            <option value="81">81</option>
                            <option value="82">82</option>
                            <option value="83">83</option>
                            <option value="84">84</option>
                            <option value="85">85</option>
                            <option value="86">86</option>
                            <option value="87">87</option>
                            <option value="88">88</option>
                            <option value="89">89</option>
                            <option value="90">90</option>
                            <option value="91">91</option>
                            <option value="92">92</option>
                            <option value="93">93</option>
                            <option value="94">94</option>
                            <option value="95">95</option>
                            <option value="971">971</option>
                            <option value="972">972</option>
                            <option value="973">973</option>
                            <option value="974">974</option>
                            <option value="976">976</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="question">Votre question :</label>
                    <textarea id="question" name="ask" required rows="4"></textarea>
                </div>
                <p>1 question / 1 envoi </p>
                <button type="submit">Envoyer ma question</button>

                <div id="form-feedback" class="form-message"></div>
            </form>
        </div>
        <div class="container">
            <a href="https://zenasso.fr" target="_blank"
                style="display: flex;flex-direction: row;text-align: center;align-items: center;color: #000;justify-content: center;"><img
                    src="https://zenasso.fr/img/ZenAsso%20logo.png" width="40" alt="logo_zenasso">Développé par
                <span style="color: #FF3131;font-weight: bold;">
                    ZenAsso</span></a>
        </div>
    </main>
    <script src="assets/js/main.js"></script>
</body>

</html>