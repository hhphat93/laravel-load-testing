import http from 'k6/http';
import { check } from 'k6';

export let options = {
    vus: 1000,
    iterations: 1000,
};

export default function () {
    const payload = JSON.stringify({
        user_id: __VU,
    });

    const params = {
        headers: { 'Content-Type': 'application/json' },
    };

    const res = http.post('http://u1/api/ecommerce/v1/checkout', payload, params);

    check(res, {
        'success (200)': (r) => r.status === 200,
        'fail handled (500)': (r) => r.status === 500,
    });
}
