# TP2 — Réponses aux questions

## Question 1 — Avantage d'un FormType séparé
Le formulaire est défini une seule fois et réutilisable : `ArticleType` sert à la fois à la création et à la modification. Le contrôleur reste court et lisible, car il ne fait que traiter la requête. On respecte la séparation des responsabilités, et le formulaire se modifie et se teste à un seul endroit.

## Question 2 — Cycle de vie d'un formulaire
1. **Affichage (GET)** : `createForm()` crée le formulaire lié à un objet vide ; `handleRequest()` ne trouve aucune donnée, donc `isSubmitted()` est faux et le formulaire vide est affiché.
2. **Soumission (POST)** : `handleRequest()` lit les données, les convertit (texte en `DateTime`, id en objet `Categorie`…) et remplit l'entité via ses setters.
3. **Validation** : `isValid()` vérifie les contraintes `#[Assert\...]` et le jeton CSRF.
4. **Invalide** : le formulaire est réaffiché avec les valeurs saisies et les messages d'erreur.
5. **Valide** : `persist()` puis `flush()` enregistrent en base, un message flash est créé, puis une redirection évite un double envoi si l'utilisateur recharge la page.

## Question 3 — form_row() ou form_label() + form_widget()
`form_row()` affiche en une fois le label, le champ, les erreurs et l'aide, avec la mise en page du thème : c'est le choix par défaut. Les fonctions séparées (`form_label()`, `form_widget()`, `form_errors()`) donnent un contrôle total du HTML ; on les préfère pour une mise en page particulière (champs côte à côte, icône dans le champ, label placé ailleurs).

## Question 4 — Validation client ou serveur
La validation client (HTML5, JavaScript) s'exécute dans le navigateur avant l'envoi : elle améliore le confort de l'utilisateur. La validation serveur (Symfony Validator) s'exécute sur le serveur avant l'enregistrement. La validation client se contourne facilement (désactiver JavaScript, modifier le HTML, envoyer une requête avec curl ou Postman). Seule la validation serveur garantit que les données enregistrées sont correctes et sûres : il faut donc toujours valider côté serveur.

## Question 5 — Migration de la relation et clé étrangère
La migration crée la table `categorie`, ajoute une colonne `categorie_id INT DEFAULT NULL` dans `article`, puis une contrainte `FOREIGN KEY (categorie_id) REFERENCES categorie (id)` et un index sur cette colonne. La propriété `articles` (OneToMany) ne crée aucune colonne : elle est calculée à partir de `categorie_id`.
Une clé étrangère est une colonne qui contient la clé primaire d'une ligne d'une autre table. La base garantit l'intégrité référentielle : impossible de référencer une catégorie inexistante, ou de supprimer une catégorie encore utilisée.

## Question 6 — createFormBuilder() ou FormType
- `createFormBuilder()` : rapide, tout au même endroit, pas de fichier en plus ; mais non réutilisable (il faudrait recopier le code pour la modification), contrôleur plus long, tests plus difficiles. Convient à un petit formulaire unique.
- FormType séparé : réutilisable, contrôleur court, séparation des responsabilités, plus facile à tester ; seul inconvénient, un fichier de plus.

## Question 7 — Pourquoi pas de persist() lors d'une modification
L'article modifié a été chargé depuis la base par Doctrine : il est déjà dans l'état *managed* (géré). `persist()` sert seulement à faire connaître à Doctrine une entité nouvelle. Le concept est l'**Unit of Work** : Doctrine garde en mémoire les entités gérées et leurs valeurs d'origine ; au `flush()`, il détecte les changements (*change tracking*) et génère automatiquement le `UPDATE`.

## Question 8 — Attaque CSRF et danger du GET
Une attaque CSRF fait exécuter une action à un utilisateur connecté à son insu : un site malveillant envoie une requête vers notre site, et le navigateur y joint automatiquement les cookies de session. Le jeton CSRF, valeur secrète liée à la session et placée dans un champ caché, bloque l'attaque, car le site malveillant ne peut pas la connaître.
Un GET de suppression est dangereux : une simple balise `<img src=".../supprimer">` suffirait à déclencher la suppression ; les robots et le préchargement des navigateurs suivent les liens ; l'URL reste dans l'historique et peut être rejouée. Par convention HTTP, GET doit être sans effet sur les données : on utilise POST (ou DELETE) avec un jeton CSRF.
