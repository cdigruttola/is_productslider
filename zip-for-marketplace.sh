rm -rf tmp
mkdir -p tmp/is_productslider
cp -R classes tmp/is_productslider
cp -R controllers tmp/is_productslider
cp -R config tmp/is_productslider
cp -R docs tmp/is_productslider
cp -R override tmp/is_productslider
cp -R sql tmp/is_productslider
cp -R src tmp/is_productslider
cp -R translations tmp/is_productslider
cp -R views tmp/is_productslider
cp -R upgrade tmp/is_productslider
cp -R vendor tmp/is_productslider
cp -R index.php tmp/is_productslider
cp -R logo.png tmp/is_productslider
cp -R is_productslider.php tmp/is_productslider
cp -R config.xml tmp/is_productslider
cp -R LICENSE tmp/is_productslider
cp -R README.md tmp/is_productslider
cd tmp && find . -name ".DS_Store" -delete
zip -r is_productslider.zip . -x ".*" -x "__MACOSX"
