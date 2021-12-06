document.getElementById('element_1').addEventListener('change', function() {
    var fr = new FileReader();
    fr.readAsText(this.files[0]);

    fr.onload = function() {
        //document.getElementById('output').textContent = fr.result;

        let data = fr.result.split(',');
        //console.log(data);
        let day = 1;
        let limit = 256;
        let startingPoint
        let nextPoint

        while (day <= limit) {
            console.log("Debut le tableau");
            data.forEach(function(fish, i, arr) {
                    if (fish == 0) {
                        arr.push(8)
                        arr[i] = 6
                    } else {
                        arr[i] = fish - 1
                    }

                })
                //console.log(data);
            console.log("Après le tableau");

            const counts = {};
            for (const num of data) {
                //console.log(num);
                counts[num] = counts[num] ? counts[num] + 1 : 1;
                //console.log(counts[num]);
            }
            //console.log(counts);
            console.log("Fin du comptage");
            //exit;
            //console.log(counts);
            let c = Object.keys(counts).length
                //console.log(c);
            if (c == 9) {
                startingPoint = counts
                startingDay = day
                day = limit
            }
            day++
        }
        console.log("Entre les deux boucles");
        console.log(startingDay);
        console.log(startingPoint);
        while (startingDay < limit) {
            nextPoint = JSON.parse(JSON.stringify(startingPoint))
            nextPoint[0] = startingPoint[1]
            nextPoint[1] = startingPoint[2]
            nextPoint[2] = startingPoint[3]
            nextPoint[3] = startingPoint[4]
            nextPoint[4] = startingPoint[5]
            nextPoint[5] = startingPoint[6]
            nextPoint[6] = startingPoint[0] + startingPoint[7]
            nextPoint[7] = startingPoint[8]
            nextPoint[8] = startingPoint[0]
            startingPoint = nextPoint
            startingDay++
            console.log(startingPoint);
        }

        let k = Object.keys(startingPoint).reduce(function(previous, key) {
            return previous + startingPoint[key];
        }, 0);

        console.log(k);



        //console.log(data);
    }




})


/*
function day06(limit, part) {
    let day = 1
    let startingPoint
    let nextPoint
    let data = [...dataInput]
    while (day <= limit) {
        data.forEach(function(fish, i, arr) {
            if (fish == 0) {
                arr.push(8)
                arr[i] = 6
            } else {
                arr[i] = fish - 1
            }
        })

        const counts = {};
        for (const num of data) {
            counts[num] = counts[num] ? counts[num] + 1 : 1;
        }

        let c = Object.keys(counts).length
        if (c == 9) {
            startingPoint = counts
            startingDay = day
            day = limit
        }
        day++
    }


    while (startingDay < limit) {
        nextPoint = JSON.parse(JSON.stringify(startingPoint))
        nextPoint[0] = startingPoint[1]
        nextPoint[1] = startingPoint[2]
        nextPoint[2] = startingPoint[3]
        nextPoint[3] = startingPoint[4]
        nextPoint[4] = startingPoint[5]
        nextPoint[5] = startingPoint[6]
        nextPoint[6] = startingPoint[0] + startingPoint[7]
        nextPoint[7] = startingPoint[8]
        nextPoint[8] = startingPoint[0]
        startingPoint = nextPoint
        startingDay++
    }


    let k = Object.keys(startingPoint).reduce(function(previous, key) {
        return previous + startingPoint[key];
    }, 0);

    console.log(`Part ${part} = ${k}`)
}

day06(80, 'one')
day06(256, 'two')*/