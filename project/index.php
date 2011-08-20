<?php include 'head.php' ?>
<span class="titre">&raquo; Accès rapide</span>
<center><a href="#cible">Cible visée </a> || <a href="#revenus">Revenus</a> || <a href="#tech">Aspect technique</a> || <a href="#contact">Contact</a></center>
<br /><br /><br />

	<span class="titre">Présentation <img src="images/infos.png" class="icone" /></span>
<div class="texte">
Teen Quotes est un site regroupant tout un tas de citations du quotidien, que l'on considère comme "vraies". Le site est principalement en anglais, mais toute l'interface est traduite en français et en anglais.
<br /><br />
Vous l'aurez sûrement remarqué, l'architecture du site ressemble beaucoup à VDM / FML... En effet le principe est globalement le même : vous pouvez proposer vos citations, les commenter, les partager.
<br /><br />
Le site est très orienté réseaux sociaux, vous trouverez beaucoup de liens de partage vers Facebook / Twitter. Je pense que le trafic principal viendra des réseaux sociaux d'ailleurs.
<br />
<h4>D'où viennent les citations ?</h4>
Les citations viennent pour la plupart de Twitter, plusieurs comptes proposent de nombreuses citations intéressantes par jour. J'en garde quelques unes que je modifie, ou non. Bien évidemment, quand je suis inspiré, il m'arrive d'en écrire moi même.</div>
	
	
	<span id="cible" class="titre">Cible visée <img src="images/target.gif" class="icone" /></span>

<div class="texte">	
La cible de ce projet est principalement les adolescents : en effet, ces citations parlent généralement des petits tracas de la vie quotidienne (amours, amis, cours, devoirs, musique, sorties...) et plus précisément féminine : les filles sont plus tracassées ou attachées à ces petites choses du quotidien.<br>

<h4>Les sources de trafic</h4>
La source de trafic visée est celle venant des réseaux sociaux : tout est fait pour que l'utilisateur partage le plus possible. En effet, les boutons bleus de partage de Twitter et Facebook ressortent bien du design général, sans pour autant choquer grâce à une reprise de la couleur dans le fond même du site.<br>
<br />
Le partage doit être immédiat, l'utilisateur doit avoir envie de partager ce qu'il a tenu pour vrai en venant de le lire. Il repose sur un sentiment impulsif.<br>

<h4>Pourquoi Twitter et Facebook ?</h4>
Ces 2 réseaux sociaux sont les plus utilisés au monde. Twitter est sont vraiment très présent dans les pays anglophones, surtout aux USA. Le concept même de ces petites citations vraies vient de Twitter : il y a de nombreux comptes, généralement avec plus de 50 000 followers qui proposent quelques unes de ces citations par jour.<br>
<br />

</div>

	

	<span id="revenus" class="titre">Revenus <img src="images/money.png" class="icone" /></span>
<div class="texte">
La source de revenus la plus adaptée pour Teen Quotes est vraisemblablement la publicité. La publicité peut facilement être placée sur la sidebar de droite : ce sont plus de 900px de hauteur qui sont disponibles sur la page proposant l'accès aux dernières citations.<br>
<br />
La navigation type d'un utilisateur se limitera certainement à la consultation de quelques citations, donc cet utilisateur utilisera toujours ce même schéma de page. En cas de besoin, on peut envisager un grand bandeau large, petit en hauteur, placé juste avant les dernières citations.<br>

<h4>Une stratégie ayant fait ses preuves</h4>
Utilisant la même architecture que VDM / FML, il est préférable d'utiliser le même système commercial qu'eux, décris juste auparavant. Dans un avenir, on peut également envisager la production de produits dérivés : de petits livrets, où quelques citations apparaîtraient.
</div>


<span id="tech" class="titre">Aspect technique <img src="images/competences.png" class="icone" /></span>
<div class="texte">
Teen Quotes est développé en xHTML / CSS / PHP et utilise une base de données MySQL. Les tables sont : comptes / citations / commentaires / citations favorites et comportent des jointures internes afin d'éviter de surcharger les requêtes SQL.<br>

<h4>Un site optimisé</h4>
Le code très propre permet d'avoir un chargement rapide, malgré le nombre de requêtes SQL important sur chaque page. Les scripts externes propres à Facebook et Twitter sont appelés en toute fin de page et ne gène pas l'affichage du chargement, comme si ils étaient placés dans l'entête.<br>
<br />
Teen Quotes utilise énormément de propriété CSS3, et ne comporte que son image de fond, répétée, comme élément pur du design. Le reste du design est en CSS, sauf pour les icones, toutes en PNG et qui sont d'une taille très petite (entre 500 octets et 5ko par image).<br>
<br />
J'ai choisi d'utiliser le script JS bien connu Roundies afin de reproduire les effets du border radius sous IE. Bien évidemment ce script est appelé en fin de page et ne gène pas le chargement.<br>
<h4>Une version mobile</h4>
Teen Quotes existe en version mobile, optimisé pour les smartphones à l'adresse <a href="http://m.teen-quotes.com" target="_blank">http://m.teen-quotes.com</a>.<br>
<br />
Ainsi les utilisateurs accédant à Teen Quotes depuis un smartphone naviguent sur un site adapté, clair et optimisé pour leur appareil mobile. Les utilisateurs trouvent rapidement et facilement les citations qu'ils veulent, toujours dans les 2 langues.<br>
<br />
L'accent est mis sur la facilité d'utilisation, et sa rapidité !<br>
<br />
<h4>Concernant la traduction de l'interface</h4>
La traduction de l'interface se fait en fonction du cookie de langue stocké chez le client. En fonction de celui ci, différents fichiers de langages sont appelés. Ainsi, tout le texte est séparé pour les 2 langues (français et anglais) et l'ajout d'une autre langue peut se faire très facilement.
</div>


<span id="contact" class="titre">Contact <img src="images/contact.png" class="icone" /></span>
<div class="texte">
<li><span class="bleu">Email :</span> antoine@augusti.fr</li>
<li><span class="bleu">Windows Live :</span> antoine@augusti.fr</li>
<li><span class="bleu">Site internet :</span> <a href="http://www.antoine-augusti.fr" target="_blank">http://www.antoine-augusti.fr</a></li>
<li><span class="bleu">Facebook :</span> <a href="http://facebook.com/AntoineAug" target="_blank">http://www.facebook.com/AntoineAugusti</a></li>
<li><span class="bleu">Twitter :</span> <a href="http://twitter.com/AntoineAug" target="_blank">http://twitter.com/AntoineAug</a></li>
<li><span class="bleu">Skype :</span> AntoineAugusti</li>
</div>


<?php include 'footer.php' ?>

