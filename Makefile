none:

buildimage:
	docker build -f Containerfile  -t vitexsoftware/abraflexi-pricefixer:latest .

buildx:
	docker buildx build  -f Containerfile  . --push --platform linux/arm/v7,linux/arm64/v8,linux/amd64 --tag vitexsoftware/abraflexi-pricefixer:latest

drun:
	docker run  -f Containerfile --env-file .env vitexsoftware/abraflexi-pricefixer:latest

phar:
	rm -rfv debian/multiflexi-abraflexi-pricefixer debian/abraflexi-pricefixer
	php -d phar.readonly=off /usr/bin/phar-composer build .
	chmod +x abraflexi-pricefixer.phar
	mv abraflexi-pricefixer.phar abraflexi-pricefixer_`dpkg-parsechangelog | sed -n 's/^Version: //p'| sed 's/~.*//'`.phar
