A place to work visuals things between graphics designer and other people of the team.
For the moment, a graphic designer has only to upload images on server throw FTP and the BetterIndex display everything in a convenient way.
BetterIndex display images with size in pixel, weight, a link to the HD version, the URL af the image and the HTML code to insert it in a web page.
BetterIndex works with conventions :
 - If a directory is called evolution/ then, the order is inverted and the newer files are displayed first. The two last number are displayed in big. That allow to the team to call a graphic with the good name. At the end, this directory keep a trace of the evolution of a graphics.
 - If a directory is called HD/ then the images are not displayed (because to heavy) and if there is a file that have the same name in the HD/ folder than in the parent folder, a link is automatically done on the image.
You can see a demo here : visuels.l214.com.
(My English is certainly bad ! Sorry, I'm French)

For the moment, no automatic transformation on images are done by BetterIndex because this tool is convenient to host graphic works and the best result are done by graphic designers.
So a graphic designer can upload an image well sized for the web and the HD / SVG file to be able to change it in the futur.
But a graphic designer won't upload the HD to get automatically the well sized file.
This is a feature for the futur but it need time, because, is not simple to automatically select the better format (PNG or JPG). And not easy to automatically select the better JPG quality for an image.

TODO :
 * Manage users to allow authenticated users to drag and drop pictures directly in BetterIndex.
 * A minimal admin to allow to move/rename files, create/move/rename directories (and style keep the old URL valid by doing a 301 Move permanently redirection).
 * A kind of forum to allow people of the team to write on picture to make comments.
 * In mode evolution/ , a way to vote for the favorite image and decide witch one will be kept.
 * Automatically resize images and selecting automatically the best format en the best level compression to keep all the details in the more lightweight file.