import { Col, Container, Row } from "react-bootstrap";
import PostList from "../components/PostList";
import RecentPosts from "../components/RecentPosts";

function HomePage() {
  return (

      <Container>

          <div className="jumbotron mt-4 bg-body-secondary p-3 rounded">
              <h1 className="display-4">Home</h1>
              <p className="lead">Explore how leveraging TypeScript in React and optimizing frontend performance can transform your development process, making applications faster, more reliable, and easier to maintain.</p>
          </div>

          <Row className="mt-4">
              <Col md="9">
                  <PostList />
              </Col>

              <Col md="3">
                  <RecentPosts />
              </Col>
          </Row>

      </Container>

  );
}

export default HomePage;