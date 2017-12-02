#!/bin/bash

product_name="DzAppCenter";
product_version="1.0"
buildtime=`date +%Y%m%d%H%M%S`
zipfile="$product_name-$product_version-$buildtime.zip"
outdir="output/$product_name"

function cpfiles()
{
    for i in $@; do
        cp -r $i $outdir
    done
}

################################
rm -rf output
mkdir -p $outdir/data
################################
cpfiles *.php config index.htm source tool md5 pack template upload
################################
cd $outdir
# 删除php文件中的所有注释代码
../../clear_annotation -r -w
################################
# zip
cd ../; zip -r $zipfile $product_name
cd ../

echo 'build success'
exit 0
