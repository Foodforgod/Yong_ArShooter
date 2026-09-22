import * as THREE from 'three';

export class EffectsSystem {
    constructor(scene) {
        this.scene = scene;
        this.particles = [];
    }
    spawnHitSpark(pos) {
        const geo = new THREE.SphereGeometry(0.03, 8, 8);
        const mat = new THREE.MeshBasicMaterial({ color: 0xffcc00 });
        const mesh = new THREE.Mesh(geo, mat);
        mesh.position.copy(pos);
        this.scene.add(mesh);
        
        this.particles.push({
            mesh: mesh,
            life: 0.2,
            maxLife: 0.2,
            velocity: new THREE.Vector3((Math.random()-0.5)*2, (Math.random()-0.5)*2, (Math.random()-0.5)*2).normalize().multiplyScalar(2)
        });
    }
    update(dt) {
        for (let i = this.particles.length - 1; i >= 0; i--) {
            const p = this.particles[i];
            p.life -= dt;
            p.mesh.position.addScaledVector(p.velocity, dt);
            p.mesh.scale.multiplyScalar(0.92);
            if (p.life <= 0) {
                this.scene.remove(p.mesh);
                p.mesh.geometry.dispose();
                p.mesh.material.dispose();
                this.particles.splice(i, 1);
            }
        }
    }
}