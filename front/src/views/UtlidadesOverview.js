import React from "react";
// import PropTypes from "prop-types";
import { Row, Col, Card, CardHeader, CardBody } from "shards-react";

import RangeDatePicker from "../components/common/RangeDatePicker";
import Chart from "../utils/chart";

let utilidadesPesos = [];
let utilidadesBille = [];
class UtlidadesOverview extends React.Component {
  constructor(props) {
    super(props);

    this.canvasRef = React.createRef();
    let utilidadesPesos = props.allUtilidades.data.utilidadesPesos;
    let utilidadesBille = props.allUtilidades.data.utilidadesBille;
  }

  componentDidMount() {
    const chartOptions = {
      ...{
        responsive: true,
        legend: {
          position: "top"
        },
        elements: {
          line: {
            // A higher value makes the line look skewed at this ratio.
            tension: 0.3
          },
          point: {
            radius: 0
          }
        },
        scales: {
          xAxes: [
            {
              gridLines: false,
              ticks: {
                callback(tick, index) {
                  // Jump every 7 values on the X axis labels to avoid clutter.
                  return index % 7 !== 0 ? "" : tick;
                }
              }
            }
          ],
          yAxes: [
            {
              ticks: {
                suggestedMax: 45,
                callback(tick) {
                  if (tick === 0) {
                    return tick;
                  }
                  // Format the amounts using Ks for thousands.
                  return tick > 999 ? `${(tick / 1000).toFixed(1)}K` : tick;
                }
              }
            }
          ]
        },
        hover: {
          mode: "nearest",
          intersect: false
        },
        tooltips: {
          custom: false,
          mode: "nearest",
          intersect: false
        }
      },
      ...this.props.chartOptions
    };

    const VarUtlidadesOverview = new Chart(this.canvasRef.current, {
      type: "LineWithLine",
      data: this.props.chartData,
      options: chartOptions
    });

    // They can still be triggered on hover.
    const buoMeta = VarUtlidadesOverview.getDatasetMeta(0);
    buoMeta.data[0]._model.radius = 0;
    buoMeta.data[
      this.props.chartData.datasets[0].data.length - 1
    ]._model.radius = 0;

    // Render the chart.
    VarUtlidadesOverview.render();
  }

  render() {
    const { title } = this.props;
    return (
      <Card small className="h-100">
        <CardHeader className="border-bottom">
          <h6 className="m-0">{title}</h6>
        </CardHeader>
        <CardBody className="pt-0">
          <Row className="border-bottom py-2 bg-light">
            <Col sm="6" className="d-flex mb-2 mb-sm-0">
              <RangeDatePicker />
              {JSON.stringify(this.utilidadesPesos)}
              {JSON.stringify(this.utilidadesBille)}
            </Col>
          </Row>
          <canvas
            height="120"
            ref={this.canvasRef}
            style={{ maxWidth: "100% !important" }}
          />
        </CardBody>
      </Card>
    );
  }
}

UtlidadesOverview.defaultProps = {
  title: "Utilidades",
  chartData: {
    labels: Array.from(new Array(30), (_, i) => (i === 0 ? 1 : i)),
    datasets: [
      {
        label: "Pesos",
        fill: "start",
        data: [
          380,
          430,
          120,
          230,
          410,
          740,
          380,
          430,
          120,
          230,
          410,
          740,
          430,
          120,
          230,
          410,
          740,
          380,
          430,
          120,
          230,
          410,
          740
        ],
        backgroundColor: "rgba(0,123,255,0.1)",
        borderColor: "rgba(0,123,255,1)",
        pointBackgroundColor: "#ffffff",
        pointHoverBackgroundColor: "rgb(0,123,255)",
        borderWidth: 1.5,
        pointRadius: 0,
        pointHoverRadius: 3
      },
      {
        label: "Bille",
        fill: "start",
        data: [
          380,
          430,
          120,
          230,
          410,
          740,
          380,
          430,
          120,
          230,
          410,
          740,
          430,
          120,
          230,
          410,
          740,
          380,
          430,
          120,
          230,
          410,
          740
        ],
        // borderDash: [3, 3],
        // borderWidth: 1,
        // pointRadius: 0,
        // pointHoverRadius: 2,
        // pointBorderColor: "rgba(255,65,105,1)"
        backgroundColor: "rgba(255,0,0,0.1)",
        borderColor: "rgba(255,0,0,1)",
        pointBackgroundColor: "#ffffff",
        pointHoverBackgroundColor: "rgb(255,0,0)",
        borderWidth: 1.5,
        pointRadius: 0,
        pointHoverRadius: 3
      }
    ]
  }
};

export default UtlidadesOverview;
