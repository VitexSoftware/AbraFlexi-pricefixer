none:

buildimage:
	docker build -f Containerfile  -t vitexsoftware/abraflexi-pricefixer:latest .

buildx:
	docker buildx build  -f Containerfile  . --push --platform linux/arm/v7,linux/arm64/v8,linux/amd64 --tag vitexsoftware/abraflexi-pricefixer:latest

drun:
	docker run  -f Containerfile --env-file .env vitexsoftware/abraflexi-pricefixer:latest

phar:
        phar-composer build .
