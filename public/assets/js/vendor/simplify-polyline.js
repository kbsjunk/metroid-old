//https://gist.github.com/adammiller/826148
google.maps.Polyline.prototype.simplifyLine = function(tolerance){
    var res = null;

    if(this.getPath() && this.getPath().getLength()){
        var points = this.getPath().getArray();

        var Line = function( p1, p2 ) {
            this.p1 = p1;
            this.p2 = p2;

            this.distanceToPoint = function( point ) {
                // slope
                var m = ( this.p2.lat() - this.p1.lat() ) / ( this.p2.lng() - this.p1.lng() ),
                    // y offset
                    b = this.p1.lat() - ( m * this.p1.lng() ),
                    d = [];
                // distance to the linear equation
                d.push( Math.abs( point.lat() - ( m * point.lng() ) - b ) / Math.sqrt( Math.pow( m, 2 ) + 1 ) );
                // distance to p1
                d.push( Math.sqrt( Math.pow( ( point.lng() - this.p1.lng() ), 2 ) + Math.pow( ( point.lat() - this.p1.lat() ), 2 ) ) );
                // distance to p2
                d.push( Math.sqrt( Math.pow( ( point.lng() - this.p2.lng() ), 2 ) + Math.pow( ( point.lat() - this.p2.lat() ), 2 ) ) );
                // return the smallest distance
                return d.sort( function( a, b ) {
                    return ( a - b ); //causes an array to be sorted numerically and ascending
                } )[0];
            };
        };

        var douglasPeucker = function( points, tolerance ) {
            if ( points.length <= 2 ) {
                return [points[0]];
            }
            var returnPoints = [],
                // make line from start to end 
                line = new Line( points[0], points[points.length - 1] ),
                // find the largest distance from intermediate poitns to this line
                maxDistance = 0,
                maxDistanceIndex = 0,
                p;
            for( var i = 1; i <= points.length - 2; i++ ) {
                var distance = line.distanceToPoint( points[ i ] );
                if( distance > maxDistance ) {
                    maxDistance = distance;
                    maxDistanceIndex = i;
                }
            }
            // check if the max distance is greater than our tollerance allows 
            if ( maxDistance >= tolerance ) {
                p = points[maxDistanceIndex];
                line.distanceToPoint( p, true );
                // include this point in the output 
                returnPoints = returnPoints.concat( douglasPeucker( points.slice( 0, maxDistanceIndex + 1 ), tolerance ) );
                // returnPoints.push( points[maxDistanceIndex] );
                returnPoints = returnPoints.concat( douglasPeucker( points.slice( maxDistanceIndex, points.length ), tolerance ) );
            } else {
                // ditching this point
                p = points[maxDistanceIndex];
                line.distanceToPoint( p, true );
                returnPoints = [points[0]];
            }
            return returnPoints;
        };
        res = douglasPeucker( points, tolerance );
        // always have to push the very last point on so it doesn't get left off
        res.push( points[points.length - 1 ] );
    }
    return res;
};

// var pl = new google.maps.Polyline({...});
// var simplifiedLinePath = pl.simplifyLine(0.00045);
// var simplifiedLine = new google.maps.Polyline({map: gmap, path: simplifiedLinePath});