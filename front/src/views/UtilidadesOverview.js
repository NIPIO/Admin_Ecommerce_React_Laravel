import React from "react";
// import PropTypes from "prop-types";
import { Row, Col, Card, CardBody } from "shards-react";

// import RangeDatePicker from "../components/common/RangeDatePicker";
import Chart from "../utils/chart";
// import Picker from "react-month-picker";

const datasetPesos = {
  label: "Pesos",
  fill: "start",
  data: [
    3500,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    1000,
    0
  ],
  backgroundColor: "rgba(0,255,0,0.1)",
  borderColor: "rgba(0,255,0,1)",
  pointBackgroundColor: "#ffffff",
  pointHoverBackgroundColor: "rgb(0,255,0)",
  borderWidth: 1.5,
  pointRadius: 0,
  pointHoverRadius: 3
};
const datasetBille = {
  label: "Bille",
  fill: "start",
  data: [
    3500,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    1000,
    0
  ],
  backgroundColor: "rgba(0,0,255,0.1)",
  borderColor: "rgba(0,0,255,1)",
  pointBackgroundColor: "#ffffff",
  pointHoverBackgroundColor: "rgb(0,0,255)",
  borderWidth: 1.5,
  pointRadius: 0,
  pointHoverRadius: 3
};

class UtilidadesOverview extends React.Component {
  constructor(props) {
    super(props);

    this.canvasRef = React.createRef();

    this.utilidadesPesos = props.allUtilidades.utilidadesPesos;
    this.utilidadesBille = props.allUtilidades.utilidadesBille;
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
              gridLines: false
              // ticks: {
              //   callback(tick, index) {
              //     // Jump every 7 values on the X axis labels to avoid clutter.
              //     return index % 1 !== 0 ? "" : tick;
              //   }
              // }
            }
          ],
          yAxes: [
            {
              ticks: {
                // suggestedMax: 45,
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

    const VarUtilidadesOverview = new Chart(this.canvasRef.current, {
      type: "line",
      data: this.props.chartData,
      options: chartOptions
    });

    const buoMeta = VarUtilidadesOverview.getDatasetMeta(0);

    datasetPesos.data.splice(0, 31); //Elimino los datos del mes anteriormente seleccionado.
    datasetBille.data.splice(0, 31); //Elimino los datos del mes anteriormente seleccionado.

    if (this.utilidadesPesos && this.utilidadesPesos) {
      datasetPesos.data.push(...this.utilidadesPesos); //Inserto los nuevos datos.
      datasetBille.data.push(...this.utilidadesBille); //Inserto los nuevos datos.

      buoMeta.data[0]._model.radius = 0;
      buoMeta.data[
        this.props.chartData.datasets[0].data.length - 1
      ]._model.radius = 0;

      // Render the chart.
      VarUtilidadesOverview.render();
    }
  }

  render() {
    // const { title } = this.props;
    return (
      <Card small className="h-100">
        {/* <CardHeader className="border-bottom">
          <h6 className="m-0">{title}</h6>
        </CardHeader> */}
        <CardBody className="pt-0">
          <Row className="border-bottom py-2 bg-light">
            <Col sm="6" className="d-flex mb-2 mb-sm-0"></Col>
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

UtilidadesOverview.defaultProps = {
  // title: "Utilidades",
  chartData: {
    labels: Array.from(new Array(31), (_, i) => (i === 0 ? 1 : i + 1)),
    datasets: [datasetPesos, datasetBille]
  }
};

export default UtilidadesOverview;
