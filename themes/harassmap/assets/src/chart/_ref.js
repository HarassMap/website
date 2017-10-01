//Code and data from:
// http://mbostock.github.io/d3/talk/20111018/area-gradient.html
//Adapted by Amelia Bellamy-Royds in response to:
// http://stackoverflow.com/questions/21050256/d3js-zoom-to-change-density-of-values

var m = [79, 80, 160, 79],
    w = 1280 - m[1] - m[3],
    h = 800 - m[0] - m[2],
    parse = d3.time.format("%Y-%m-%d").parse,
    format = d3.time.format("%Y");

// Scales. Note the inverted domain for the y-scale: bigger is up!
var x = d3.time.scale().range([0, w]),
    y = d3.scale.linear().range([h, 0]),
    xAxis = d3.svg.axis().scale(x).orient("bottom").tickSize(-h, 0).tickPadding(6),
    yAxis = d3.svg.axis().scale(y).orient("right").tickSize(-w).tickPadding(6);

// An area generator.
var area = d3.svg.area()
    .interpolate("step-after")
    .x(function(d) { return x(d.date); })
    .y0(y(0))
    .y1(function(d) { return y(d.value); });

// A line generator.
var line = d3.svg.line()
    .interpolate("step-after")
    .x(function(d) { return x(d.date); })
    .y(function(d) { return y(d.value); });

var svg = d3.select("body").append("svg:svg")
    .attr("width", w + m[1] + m[3])
    .attr("height", h + m[0] + m[2])
    .append("svg:g")
    .attr("transform", "translate(" + m[3] + "," + m[0] + ")");

var gradient = svg.append("svg:defs").append("svg:linearGradient")
    .attr("id", "gradient")
    .attr("x2", "0%")
    .attr("y2", "100%");

gradient.append("svg:stop")
    .attr("offset", "0%")
    .attr("stop-color", "#fff")
    .attr("stop-opacity", .5);

gradient.append("svg:stop")
    .attr("offset", "100%")
    .attr("stop-color", "#999")
    .attr("stop-opacity", 1);

svg.append("svg:clipPath")
    .attr("id", "clip")
    .append("svg:rect")
    .attr("x", x(0))
    .attr("y", y(1))
    .attr("width", x(1) - x(0))
    .attr("height", y(0) - y(1));

svg.append("svg:g")
    .attr("class", "y axis")
    .attr("transform", "translate(" + w + ",0)");

svg.append("svg:path")
    .attr("class", "area")
    .attr("clip-path", "url(#clip)")
    .style("fill", "url(#gradient)");

svg.append("svg:g")
    .attr("class", "x axis")
    .attr("transform", "translate(0," + h + ")");

svg.append("svg:path")
    .attr("class", "line")
    .attr("clip-path", "url(#clip)");

var Zoomer = d3.behavior.zoom()
    .x(x) // link to the xScale
    .on("zoom", zoom);
svg.append("svg:rect")  // overlay to catch mouse zoom events
    .attr("class", "pane")
    .attr("width", w)
    .attr("height", h)
    .call(Zoomer);

//d3.csv("flights-departed.csv", function(data) {
data = getData();
//console.log(data.length, data[0]);
// Parse dates and numbers.
data.forEach(function(d) {
    d.date = parse(d.date);
    d.value = +d.value;
});

var AllData = {};
AllData.daily=data;  //original data

AllData.weekly = d3.nest().key(function(d){
    return d3.time.week.floor(d.date);
    //nested by the start of the week
})
    .entries(data);
//call the nest function on the data

AllData.weekly.forEach(meanDaily);
//scan through the nested array, and replace
//the nested values with their mean

AllData.monthly = d3.nest().key(function(d){
    return d3.time.month.floor(d.date);
})
    .entries(data);
AllData.monthly.forEach(meanDaily);

AllData.yearly = d3.nest().key(function(d){
    return d3.time.year.floor(d.date);
})
    .entries(data);
AllData.yearly.forEach(meanDaily);


//console.log(AllData.daily[0]);
//console.log(AllData.weekly[0]);
//console.log(AllData.monthly[0]);
//console.log(AllData.yearly[0]);

function meanDaily(nestedObject, i, array){
    //This function is passed to the Array.forEach() method
    //which passes in:
    // (1) the element in the array
    // (2) the element's index, and
    // (3) the array as a whole

    //It replaces the element in the array
    //(an object with properties key and values, where
    // values is an array of nested objects)
    //with a single object that has the nesting key
    //value parsed back into a date,
    //and the mean of the nested values' value
    //as the value.  It makes sense, I swear.
    array[i] = {date:new Date(nestedObject.key),
        value:d3.mean(nestedObject.values,
            function(d) {return d.value;}
        )};
}

// Compute the maximum price.
x.domain([new Date(1988, 0, 1), new Date(2008, 0, 0)]);
Zoomer.x(x) // update the zooming behavior to match the domain
y.domain([0, d3.max(data, function(d) { return d.value; })]);

draw(AllData.yearly);
//start with entire data set, yearly averages
//});

function draw(data) {
    //Note that data is now a parameter passed in to the
    //draw method.

    //MOVED FROM THE INITIALIZATION:
    // Bind the data to our path elements.
    svg.select("path.area").data([data]);
    svg.select("path.line").data([data]);


    svg.select("g.x.axis").call(xAxis);
    svg.select("g.y.axis").call(yAxis);
    svg.select("path.area").attr("d", area);
    svg.select("path.line").attr("d", line);
    d3.select("#footer span").text("U.S. Commercial Flights, " + x.domain().map(format).join("-"));
}

function zoom() {
    //d3.event.transform(x); //From original -- v2.4 API

    //d3v3 automatically links the scale to the
    //zoom behavior object during initialization
    //and updates the x scale's domain as you zoom

    // console.log(d3.event);
    //console.log(x.domain(), x.range());

    //NEW functionality
    //get the new x domain from the scale and
    //select an appropriate data set

    var visibleDomain = x.domain();
    //calculate the number of months in the domain:
    //this is not as precise as calculating the actual
    //difference in time points, but it avoids having
    //to work with spans of trillions of millisecond
    var span = (visibleDomain[1].getYear()*12 +
        visibleDomain[1].getMonth()) -
        (visibleDomain[0].getYear()*12 +
            visibleDomain[0].getMonth());
    //console.log(visibleDomain, span);

    var startIndex = 0, endIndex, length;
    //span of indices in the chosen data array
    //that cover the visible domain
    //(endIndex being first value *beyond* the domain)
    var chosenData;
    if (span <=4 ) {
        //fewer than four months, graph daily values
        chosenData = AllData.daily;
    }
    else if (span <=24) {
        //four months to two years, graph weekly averages
        chosenData = AllData.weekly;
    }
    else if (span <= 120) {
        //two years to ten years, graph monthly averages
        chosenData = AllData.monthly;

    }
    else {
        chosenData = AllData.yearly;
    }

    while(chosenData[startIndex].date < visibleDomain[0])
        startIndex++;
    endIndex = startIndex +1;
    length = chosenData.length;
    while( (endIndex < length)&&
    (chosenData[endIndex].date < visibleDomain[1]))
        endIndex++;
    //You could probably use a better method for finding
    //the relevant region; this is going to be very slow
    //when zooming in to a small span of dates near the end
    //of the data set!

    //console.log([startIndex, endIndex]);
    //console.log(chosenData);
    draw(chosenData.slice(startIndex, endIndex));

}