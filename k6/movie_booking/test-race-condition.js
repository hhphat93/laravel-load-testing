import http from 'k6/http';
import { check } from 'k6';

export let options = {
    vus: 200,
    iterations: 200,
};

export default function () {
    const payload = JSON.stringify({
        user_id: __VU,
        showtime_id: 1,
        seat_id: 1
    });

    const params = {
        headers: { 'Content-Type': 'application/json' },
    };

    const res = http.post('http://u1/api/movie-booking/v1/reservation-seats', payload, params);

    check(res, {
        'success (200)': (r) => r.status === 200,
        'fail handled (500)': (r) => r.status === 500,
    });
}
