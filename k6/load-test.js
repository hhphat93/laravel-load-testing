import http from 'k6/http';
import { sleep } from 'k6';

export const options = {
  // A number specifying the number of VUs to run concurrently.
  // vus: 500,
  // A string specifying the total duration of the test run.
  // duration: '30s',

  stages: [
    { duration: '30s', target: 200 },
    // { duration: '1m30s', target: 500 },
    // { duration: '20s', target: 0 },
  ],

  // The following section contains configuration options for execution of this
  // test script in Grafana Cloud.
  //
  // See https://grafana.com/docs/grafana-cloud/k6/get-started/run-cloud-tests-from-the-cli/
  // to learn about authoring and running k6 test scripts in Grafana k6 Cloud.
  //
  // cloud: {
  //   // The ID of the project to which the test is assigned in the k6 Cloud UI.
  //   // By default tests are executed in default project.
  //   projectID: "",
  //   // The name of the test in the k6 Cloud UI.
  //   // Test runs with the same name will be grouped.
  //   name: "script.js"
  // },

  // Uncomment this section to enable the use of Browser API in your tests.
  //
  // See https://grafana.com/docs/k6/latest/using-k6-browser/running-browser-tests/ to learn more
  // about using Browser API in your test scripts.
  //
  // scenarios: {
    // The scenario name appears in the result summary, tags, and so on.
    // You can give the scenario any name, as long as each name in the script is unique.
    // ui: {
    //   // Executor is a mandatory parameter for browser-based tests.
    //   // Shared iterations in this case tells k6 to reuse VUs to execute iterations.
    //   //
    //   // See https://grafana.com/docs/k6/latest/using-k6/scenarios/executors/ for other executor types.
    //   executor: 'shared-iterations',
    //   options: {
    //     browser: {
    //       // This is a mandatory parameter that instructs k6 to launch and
    //       // connect to a chromium-based browser, and use it to run UI-based
    //       // tests.
    //       type: 'chromium',
    //     },
    //   },
    // },
    // rps_test: {
    //   executor: 'ramping-arrival-rate', // tăng dần RPS
    //   startRate: 10,      // bắt đầu 10 req/s
    //   timeUnit: '1s',     // tính RPS mỗi giây
    //   preAllocatedVUs: 50, // số VUs khởi tạo
    //   maxVUs: 200,         // max VUs để đạt target RPS
    //   stages: [
    //     { target: 100, duration: '20s' }, // ramp 10 → 100 req/s trong 20s
    //   ],
    // },
    // fixed_rps: {
    //   executor: 'constant-arrival-rate',
    //   rate: 2000, // số request mỗi giây (RPS)
    //   timeUnit: '1s', // đơn vị tính cho rate
    //   duration: '30s', // thời gian test
    //   preAllocatedVUs: 50, // số VU khởi tạo sẵn
    //   maxVUs: 100, // số VU tối đa có thể dùng
    // },
    // ramp_rps: {
    //   executor: 'ramping-arrival-rate',
    //   startRate: 10,   // bắt đầu từ 10 RPS
    //   timeUnit: '1s',
    //   preAllocatedVUs: 20,
    //   maxVUs: 200,
    //   stages: [
    //     { target: 50, duration: '10s' },
    //     { target: 100, duration: '20s' },
    //     { target: 200, duration: '10s' },
    //   ],
    // },

  // }
};

// The function that defines VU logic.
//
// See https://grafana.com/docs/k6/latest/examples/get-started-with-k6/ to learn more
// about authoring k6 scripts.
//
export default function () {
  // http.get('http://lb/load-test');
  http.get('http://u1/load-test');
  // http.get('http://u2/load-test');
  sleep(1);
}
