
split_parallel() {
    DIRPATH=$1
    PACKAGENAME=$2
    HEAD=$3
    TAG=$4
    mkdir $PACKAGENAME
    pushd $PACKAGENAME
    git subsplit init git@github.com:apikr/apikr.git
    if [ ! -z "$TAG" ]; then
        git subsplit publish --heads="$HEAD" --tags="$TAG" src/$DIRPATH:git@github.com:apikr/$PACKAGENAME.git
    else
        git subsplit publish --heads="$HEAD" --no-tags src/$DIRPATH:git@github.com:apikr/$PACKAGENAME.git
    fi
    popd
    rm -rf $PACKAGENAME
}

TAG=$1
PIDS=()

split_parallel Api              api                 "master" $TAG & PIDS+=($!)
split_parallel Aligo/Sms        aligo-sms           "master" $TAG & PIDS+=($!)
split_parallel ApiStore/Sms     apistore-sms        "master" $TAG & PIDS+=($!)
split_parallel Paygate/Seyfert  paygate-seyfert     "master" $TAG & PIDS+=($!)
split_parallel SKPlanet/TMap    skplanet-tmap       "master" $TAG & PIDS+=($!)
split_parallel Siot/Iamport     siot-iamport        "master" $TAG & PIDS+=($!)

for PID in "${PIDS[@]}"
do
	wait $PID
done
