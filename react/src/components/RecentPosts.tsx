import { useEffect, useState } from 'react';
import type Post from '../services/Post';
import PostService from '../services/PostService';

function RecentPosts() {

    const [posts, setPosts] = useState<Post[]>();

    useEffect(() => {
        populatePosts();
    }, []);

    function populatePosts() {
        const service = new PostService();
        const data = service.get();
        const reduced = data.reverse().slice(0, 5);
        setPosts(reduced);
    }

    return (
        <div className="bg-body-secondary px-4 py-2 rounded">
            <h4>Recent posts</h4>
            {posts === undefined ? <div>Loading posts</div> : (posts.length === 0 ?
                <div>No posts</div> :
                <ul>
                    {posts.map((post) => (
                        <li key={post.id}>
                            <button className="btn btn-link p-0 text-start" onClick={() => window.navigate(`/post/${post.id}`)}>{post.title.substring(0, 25) + " ..."}</button>
                        </li>
                    ))}
                </ul>
            )}
        </div>
    );
}

export default RecentPosts;